<?php

namespace Controller;

use Model\Department;
use Model\Phone;
use Model\Room;
use Model\User;
use Model\Role;
use Src\Request;
use Src\View;
use Src\Auth\Auth;

class PhoneController
{
    public function index(Request $request): string
    {
        $phones = Phone::all();
        return (new View())->render('site.phones', ['phones' => $phones]);
    }

    public function create(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/phones');
        }

        $rooms = Room::all();
        $users = User::all();
        return (new View())->render('site.phone_create', ['rooms' => $rooms, 'users' => $users]);
    }

    public function store(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/phones');
        }

        Phone::create([
            'number' => $request->number,
            'room_id' => $request->room_id,
            'user_id' => $request->user_id,
        ]);

        app()->route->redirect('/phones');
    }

    public function byDepartment(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        // Используем метод get() для получения параметра
        $departmentId = $request->get('department_id');

        if (!$departmentId) {
            // Обработка случая, когда параметр не передан
            return (new View())->render('site.phones', [
                'phones' => [],
                'error' => 'Не указано подразделение'
            ]);
        }

        $phones = Phone::whereHas('user', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->get();

        return (new View())->render('site.phones', [
            'phones' => $phones
        ]);
    }

    public function byRoom(Request $request): string
    {
        $roomId = $request->room_id ?? null;
        $phones = Phone::query();

        if ($roomId) {
            $phones->where('room_id', $roomId);
        }

        $rooms = Room::all();
        return (new View())->render('site.phones_by_room', [
            'phones' => $phones->get(),
            'rooms' => $rooms,
            'selectedRoom' => $roomId
        ]);
    }

    // Методы для работы с пользователями (доступны админу и сисадмину)
    public function users(Request $request): string
    {
        $users = User::all();
        return (new View())->render('site.users', ['users' => $users]);
    }

    public function createUser(Request $request): string
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        $departments = Department::all();
        $roles = Role::all();
        return (new View())->render('site.user_create', [
            'departments' => $departments,
            'roles' => $roles
        ]);
    }

    public function storeUser(Request $request): void
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            app()->route->redirect('/users');
        }

        User::create([
            'surname' => $request->surname,
            'name' => $request->name,
            'patronymic' => $request->patronymic,
            'login' => $request->login,
            'password' => $request->password,
            'department_id' => $request->department_id,
            'role_id' => $request->role_id
        ]);

        app()->route->redirect('/users');
    }


}