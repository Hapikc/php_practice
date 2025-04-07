<?php

namespace Controller;

use Model\User;
use Model\Department;
use Model\Role;
use Src\Request;
use Src\View;
use Src\Auth\Auth;

class UserController
{
    public function index(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $search = $request->search ?? null;
        $sort = $request->sort ?? 'surname';
        $order = $request->order ?? 'asc';
        $role_id = $request->role_id ?? null;

        $users = User::query();

        if ($search) {
            $users->where(function($query) use ($search) {
                $query->where('surname', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('patronymic', 'like', "%{$search}%")
                    ->orWhere('login', 'like', "%{$search}%");
            });
        }

        if ($role_id) {
            $users->where('role_id', $role_id);
        }

        $users->orderBy($sort, $order)
            ->with(['role']); // Убрали 'department' из with()

        return (new View())->render('site.users', [
            'users' => $users->get(),
            'roles' => Role::all(), // Убрали departments из передаваемых данных
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'selected_role' => $role_id
        ]);
    }

    public function create(Request $request): string
    {
        // Только для админа
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        return (new View())->render('site.user_create', [
            'roles' => Role::all() // Убрали departments
        ]);
    }


    public function edit(Request $request): string
    {
        // Только для админа
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        $user = User::find($request->user_id);
        return (new View())->render('site.user_edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }



    public function store(Request $request): string
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/hello');
            return ''; // Добавлен явный return
        }

        try {
            $validated = $this->validate($request, [
                'surname' => ['required'],
                'name' => ['required'],
                'login' => ['required', 'unique:users,login'],
                'password' => ['required', 'min:6'],
                'role_id' => ['required', 'exists:roles,role_id'],
                'avatar' => ['file'],
            ]);

            $validated['password'] = md5($validated['password']);

            // Загрузка аватара
            if ($request->hasFile('avatar')) {
                $avatarPath = (new User())->uploadAvatar($request);
                if ($avatarPath) {
                    $validated['avatar'] = $avatarPath;
                }
            }

            User::create($validated);
            app()->route->redirect('/users');
            return ''; // Добавлен явный return
        } catch (\Exception $e) {
            return (new View())->render('site.error', ['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request): string
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/hello');
            return ''; // Добавлен явный return
        }

        try {
            $user = User::find($request->user_id);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $validated = $this->validate($request, [
                'surname' => ['required'],
                'name' => ['required'],
                'login' => ['required', 'unique:users,login,' . $user->id],
                'role_id' => ['required', 'exists:roles,role_id'],
                'avatar' => ['file'],
            ]);

            if (!empty($request->password)) {
                $validated['password'] = md5($request->password);
            }

            // Удаление аватара, если отмечено
            if ($request->remove_avatar && $user->avatar) {
                $this->deleteAvatarFile($user->avatar);
                $validated['avatar'] = null;
            }

            // Загрузка нового аватара
            if ($request->hasFile('avatar')) {
                $this->deleteAvatarFile($user->avatar);
                $avatarPath = $user->uploadAvatar($request);
                if ($avatarPath) {
                    $validated['avatar'] = $avatarPath;
                }
            }

            $user->update($validated);
            app()->route->redirect('/users');
            return ''; // Добавлен явный return
        } catch (\Exception $e) {
            return (new View())->render('site.error', ['message' => $e->getMessage()]);
        }
    }

    private function deleteAvatarFile(?string $path): void
    {
        if ($path && file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $path);
        }
    }
    public function delete(Request $request): void
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        $user = User::find($request->user_id);

        // Удаляем аватар, если он есть
        if ($user->avatar && file_exists($_SERVER['DOCUMENT_ROOT'] . $user->avatar)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $user->avatar);
        }

        $user->delete();
        app()->route->redirect('/users');
    }
    private function validate(Request $request, array $rules): array
    {
        $data = $request->all();
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field][] = "Поле обязательно для заполнения";
                }

                if (strpos($rule, 'min:') === 0 && strlen($data[$field]) < substr($rule, 4)) {
                    $errors[$field][] = "Минимальная длина ".substr($rule, 4);
                }

                if ($rule === 'unique:users,login' && User::where('login', $data[$field])->exists()) {
                    $errors[$field][] = "Логин уже занят";
                }

                // Перенесено внутрь цикла
                if ($rule === 'file' && $request->hasFile($field)) {
                    $file = $request->file($field);
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!in_array($file['type'], $allowedTypes)) {
                        $errors[$field][] = "Недопустимый тип файла. Разрешены только JPEG, PNG и GIF";
                    }

                    if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                        $errors[$field][] = "Файл слишком большой. Максимальный размер 2MB";
                    }
                }
            }
        }

        if (!empty($errors)) {
            throw new \Exception("Validation failed: ".print_r($errors, true));
        }

        return $data;
    }

    public function isSysadmin(): bool
    {
        return $this->role_id === 2; //
    }

}