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
        if (!Auth::check() || !Auth::user()->isAdminOrSysadmin()) {
            app()->route->redirect('/hello');
        }

        $departments = Department::all();
        $rooms = Room::all();

        $selectedDepartment = $request->get('department_id');
        $selectedRoom = $request->get('room_id');

        $phones = Phone::query()
            ->with(['room.department', 'user'])
            ->when($selectedRoom, function($query) use ($selectedRoom) {
                $query->where('room_id', $selectedRoom);
            })
            ->when($selectedDepartment, function($query) use ($selectedDepartment) {
                $query->whereHas('room', function($q) use ($selectedDepartment) {
                    $q->where('department_id', $selectedDepartment);
                });
            })
            ->get();

        return (new View())->render('site.phones_by_room', [
            'phones' => $phones,
            'departments' => $departments,
            'rooms' => $rooms,
            'selectedDepartment' => $selectedDepartment,
            'selectedRoom' => $selectedRoom
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

    public function assignUser($phone_id)
    {
        if (!Auth::user()->isAdmin()) {
            app()->route->redirect('/hello');
        }

        $phone = Phone::find($phone_id);
        $users = User::all();

        if (isset($_POST['user_id'])) {
            $phone->user_id = $_POST['user_id'];
            $phone->save();
            app()->session->setFlash('success', 'Абонент успешно прикреплен');
            app()->route->redirect('/phones');
        }

        return new View('phones.assign', [
            'phone' => $phone,
            'users' => $users
        ]);
    }

    public function attachUser(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $phone = Phone::find($request->phone_id);
        $users = User::all();

        return (new View())->render('site.phone_attach', [
            'phone' => $phone,
            'users' => $users
        ]);
    }

    public function storeAttachedUser(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $phone = Phone::find($request->phone_id);
        $phone->update([
            'user_id' => $request->user_id
        ]);

        app()->route->redirect('/phones');
    }

    public function countByDepartment(): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $stats = Department::withCount('users')->get();

        return (new View())->render('site.count_by_department', [
            'stats' => $stats
        ]);
    }

    public function countByRoom(): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $stats = Room::withCount('phones')->get();

        return (new View())->render('site.count_by_room', [
            'stats' => $stats
        ]);
    }

    public function byUser(Request $request): string
    {
        if (!Auth::check() || !Auth::user()->isAdminOrSysadmin()) {
            app()->route->redirect('/hello');
        }

        $users = User::all();
        $selectedUser = $request->get('user_id');

        $phones = Phone::query()
            ->with(['room.department', 'user'])
            ->when($selectedUser, function($query) use ($selectedUser) {
                $query->where('user_id', $selectedUser);
            })
            ->get();

        return (new View())->render('site.phones_by_user', [
            'phones' => $phones,
            'users' => $users,
            'selectedUser' => $selectedUser
        ]);
    }

}