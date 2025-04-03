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
        $department_id = $request->department_id ?? null;
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

        if ($department_id) {
            $users->where('department_id', $department_id);
        }

        if ($role_id) {
            $users->where('role_id', $role_id);
        }

        $users->orderBy($sort, $order)
            ->with(['department', 'role']);

        return (new View())->render('site.users', [
            'users' => $users->get(),
            'departments' => Department::all(),
            'roles' => Role::all(),
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'selected_department' => $department_id,
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
            'departments' => Department::all(),
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request): void
    {
        // Только для админа
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        // Валидация данных
        $validated = $this->validate($request, [
            'surname' => ['required'],
            'name' => ['required'],
            'login' => ['required', 'unique:users,login'],
            'password' => ['required', 'min:6'],
            'role_id' => ['required', 'exists:roles,role_id'],
            'department_id' => ['required', 'exists:departments,department_id']
        ]);

        // Хеширование пароля
        $validated['password'] = md5($validated['password']);

        User::create($validated);
        app()->route->redirect('/users');
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
            'departments' => Department::all(),
            'roles' => Role::all()
        ]);
    }

    public function update(Request $request): void
    {
        // Только для админа
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        $user = User::find($request->user_id);

        $validated = $this->validate($request, [
            'surname' => ['required'],
            'name' => ['required'],
            'login' => ['required', 'unique:users,login,'.$user->id],
            'role_id' => ['required', 'exists:roles,role_id'],
            'department_id' => ['required', 'exists:departments,department_id']
        ]);

        // Если пароль изменён
        if (!empty($request->password)) {
            $validated['password'] = md5($request->password);
        }

        $user->update($validated);
        app()->route->redirect('/users');
    }

    public function delete(Request $request): void
    {
        // Только для админа
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        User::find($request->user_id)->delete();
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