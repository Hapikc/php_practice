<?php

namespace Controller;

use Model\Room;
use Model\Department;
use Src\Request;
use Src\View;
use Src\Auth\Auth;


class RoomController
{
    public function index(Request $request): string
    {
        $user = Auth::user(); // Получаем пользователя один раз

        if (!$user || !in_array($user->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $search = $request->get('search') ?? null;
        $sort = $request->get('sort') ?? 'name';
        $order = $request->get('order') ?? 'asc';

        $rooms = Room::with('department')->orderBy($sort, $order);

        if ($search) {
            $rooms->where('name', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
        }

        return (new View())->render('site.rooms', [
            'rooms' => $rooms->get(),
            'departments' => Department::all(),
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'isAdmin' => $user && in_array($user->role_id, [1, 2]) // Добавляем флаг
        ]);
    }

    public function create(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/rooms');
        }

        return (new View())->render('site.room_create', [
            'departments' => Department::all()
        ]);
    }

    public function store(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/rooms');
        }

        Room::create([
            'name' => $request->name,
            'type' => $request->type,
            'department_id' => $request->department_id
        ]);

        app()->route->redirect('/rooms');
    }

    public function edit(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/rooms');
        }

        $room = Room::find($request->room_id);
        return (new View())->render('site.room_edit', [
            'room' => $room,
            'departments' => Department::all()
        ]);
    }

    public function update(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/rooms');
        }

        $room = Room::find($request->room_id);
        $room->update([
            'name' => $request->name,
            'type' => $request->type,
            'department_id' => $request->department_id
        ]);

        app()->route->redirect('/rooms');
    }

    public function delete(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/rooms');
        }

        Room::find($request->room_id)->delete();
        app()->route->redirect('/rooms');
    }
}