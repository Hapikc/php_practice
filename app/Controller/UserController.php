<?php

namespace Controller;

use Model\User;
use Model\Role;
use Src\Request;
use Src\View;
use Src\Auth\Auth;
use Src\Validator\UserValidator;

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
        }

        try {
            $validator = new UserValidator();
            $validated = $validator->validate($request);

            $validated['password'] = md5($validated['password']);

            if ($request->hasFile('avatar')) {
                $avatarPath = (new User())->uploadAvatar($request);
                if ($avatarPath) {
                    $validated['avatar'] = $avatarPath;
                }
            }

            User::create($validated);
            app()->route->redirect('/users');
            return '';
        } catch (\InvalidArgumentException $e) {
            $request->errors = json_decode($e->getMessage(), true);
            return $this->create($request);
        } catch (\Exception $e) {
            $request->errors = ['Ошибка при создании пользователя'];
            return $this->create($request);
        }
    }

    public function update(Request $request): string
    {
        // Проверка прав доступа (только для администратора)
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/hello');
            return '';
        }

        try {
            // Создаем экземпляр валидатора
            $validator = new UserValidator();

            // Валидируем данные с флагом isUpdate = true
            $validated = $validator->validate($request, true);

            // Находим пользователя для обновления
            $user = User::find($request->user_id);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }

            // Хешируем пароль, если он был изменен
            if (!empty($request->password)) {
                $validated['password'] = md5($request->password);
            }

            // Обработка аватара - удаление старого
            if ($request->remove_avatar && $user->avatar) {
                $this->deleteAvatarFile($user->avatar);
                $validated['avatar'] = null;
            }

            // Обработка аватара - загрузка нового
            if ($request->hasFile('avatar')) {
                // Удаляем старый аватар
                $this->deleteAvatarFile($user->avatar);

                // Загружаем новый
                $avatarPath = $user->uploadAvatar($request);
                if ($avatarPath) {
                    $validated['avatar'] = $avatarPath;
                }
            }

            // Обновляем данные пользователя
            $user->update($validated);

            // Перенаправляем после успешного обновления
            app()->route->redirect('/users');
            return '';

        } catch (\InvalidArgumentException $e) {
            // Обработка ошибок валидации
            $request->errors = json_decode($e->getMessage(), true);
            return $this->edit($request);

        } catch (\Exception $e) {
            // Обработка других ошибок
            $request->errors = ['Ошибка при обновлении пользователя: ' . $e->getMessage()];
            return $this->edit($request);
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