<?php

namespace Controller;

use Model\User;
use Model\Role;
use Src\Request;
use Src\View;
use Src\Auth\Auth;
use Src\Validator\Validator;
use Collect\Collect;

class UserController
{
    public function index(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $search = $request->search ?? null;
        $role_id = $request->role_id ?? null;

        $users = User::query();

        if ($search) {
            $users->where(function($query) use ($search) {
                $query->where('surname', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('login', 'like', "%{$search}%");
            });
        }

        if ($role_id) {
            $users->where('role_id', $role_id);
        }

        $users->with(['role']);
        $userData = $users->get();

        // Использование Collect для работы с данными
        $userCollection = new Collect($userData->toArray());

        // Примеры использования Collect:

        // Группировка пользователей по ролям
        $usersByRole = $userCollection->groupBy('role_id');

        // Получение списка логинов
        $logins = $userCollection->pluck('login');

        // Фильтрация администраторов
        $admins = $userCollection->filter(function($user) {
            return $user['role_id'] == 1;
        });

        // Статистика
        $userCount = $userCollection->count();
        $adminCount = $admins->count();

        return (new View())->render('site.users', [
            'users' => $userData,
            'roles' => Role::all(),
            'search' => $search,
            'selected_role' => $role_id,
            'userCollection' => $userCollection,
            'stats' => [
                'total' => $userCount,
                'admins' => $adminCount
            ]
        ]);
    }

    public function create(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/users');
        }

        return (new View())->render('site.user_create', [
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/users');
        }

        try {
            $validated = $this->validate($request, [
                'surname' => ['required'],
                'name' => ['required'],
                'login' => ['required', 'unique:users,login'],
                'password' => ['required', 'min:6'],
                'role_id' => ['required', 'exists:roles,role_id'],
            ]);

            $validated['password'] = md5($validated['password']);

            // Обработка загрузки аватарки
            if (!empty($_FILES['avatar']['name'])) {
                $avatar = $this->uploadAvatar($_FILES['avatar']);
                if ($avatar) {
                    $validated['avatar'] = $avatar;
                }
            }

            User::create($validated);
            app()->route->redirect('/users');

        } catch (\Exception $e) {
            return (new View())->render('site.user_create', [
                'roles' => Role::all(),
                'error' => $e->getMessage(),
                'old' => $request->all()
            ]);
        }
    }

    public function edit(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/users');
        }

        $user_id = $request->user_id;

        if (!$user_id) {
            throw new \Exception("ID пользователя не указан");
        }

        $user = User::find($user_id);

        if (!$user) {
            throw new \Exception("Пользователь с ID $user_id не найден");
        }

        return (new View())->render('site.user_edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

    public function update(Request $request)
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        try {
            $user_id = $request->user_id;

            if (!$user_id) {
                throw new \Exception("ID пользователя не указан");
            }

            $user = User::find($user_id);

            if (!$user) {
                throw new \Exception("Пользователь не найден");
            }

            $validated = $this->validate($request, [
                'surname' => ['required'],
                'name' => ['required'],
                'login' => ['required', 'unique:users,login,'.$user->id],
                'role_id' => ['required', 'exists:roles,role_id'],
            ]);

            // Обработка загрузки аватарки
            if (!empty($_FILES['avatar']['name'])) {
                $avatar = $this->uploadAvatar($_FILES['avatar']);
                if ($avatar) {
                    // Удаляем старую аватарку если есть
                    if ($user->avatar) {
                        $this->deleteAvatar($user->avatar);
                    }
                    $validated['avatar'] = $avatar;
                }
            }

            if (!empty($request->password)) {
                // Валидация пароля
                $passwordValidator = new Validator(
                    ['password' => $request->password],
                    ['password' => ['min:6']],
                    ['min' => 'Пароль должен содержать не менее :arg0 символов']
                );

                if ($passwordValidator->fails()) {
                    throw new \Exception($passwordValidator->getFirstError());
                }

                $validated['password'] = md5($request->password);
            }

            $user->update($validated);
            app()->route->redirect('/users');

        } catch (\Exception $e) {
            $user = User::find($request->user_id);
            $roles = Role::all();

            return (new View())->render('site.user_edit', [
                'user' => $user,
                'roles' => $roles,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function delete(Request $request): void
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        User::find($request->user_id)->delete();
        app()->route->redirect('/users');
    }

    private function validate(Request $request, array $rules): array
    {
        $data = $request->all();

        $validator = new Validator($data, $rules, [
            'required' => 'Поле :field обязательно для заполнения',
            'min' => 'Поле :field должно содержать не менее :arg0 символов',
            'unique' => 'Значение поля :field уже существует',
            'exists' => 'Указанное значение в поле :field не существует',
            'email' => 'Поле :field должно содержать корректный email адрес',
            'numeric' => 'Поле :field должно быть числом'
        ]);

        if ($validator->fails()) {
            throw new \Exception(implode(", ", $validator->getErrorsFlat()));
        }

        return $data;
    }

    private function uploadAvatar(array $file): ?string
    {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/avatars/';

        // Проверяем тип файла
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception("Разрешены только изображения JPG, PNG и GIF");
        }

        // Проверяем размер файла (максимум 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \Exception("Размер файла не должен превышать 2MB");
        }

        // Генерируем уникальное имя файла
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Пытаемся загрузить файл
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        throw new \Exception("Ошибка при загрузке файла");
    }

    private function deleteAvatar(string $filename): void
    {
        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/avatars/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    public function deleteAvatarAction(Request $request): void
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        $user = User::find($request->user_id);
        if ($user && $user->avatar) {
            $this->deleteAvatar($user->avatar);
            $user->update(['avatar' => null]);
        }

        app()->route->redirect('/users/edit?user_id=' . $request->user_id);
    }
}