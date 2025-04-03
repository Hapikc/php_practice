<?php

namespace Controller;

use Model\Department;
use Src\Request;
use Src\View;
use Src\Auth\Auth;

class DepartmentController
{
    public function index(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }

        $search = $request->get('search') ?? null;
        $sort = $request->get('sort') ?? 'name';
        $order = $request->get('order') ?? 'asc';

        $departments = Department::query();

        if ($search) {
            $departments->where('name', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
        }

        $departments->orderBy($sort, $order);

        return (new View())->render('site.departments', [
            'departments' => $departments->get(),
            'search' => $search,
            'sort' => $sort,
            'order' => $order
        ]);
    }

    public function create(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/departments');
        }

        return (new View())->render('site.department_create');
    }

    public function store(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/departments');
        }

        Department::create([
            'name' => $request->name,
            'type' => $request->type
        ]);

        app()->route->redirect('/departments');
    }

    public function edit(Request $request): string
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/departments');
        }

        $department = Department::find($request->department_id);
        return (new View())->render('site.department_edit', [
            'department' => $department
        ]);
    }

    public function update(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/departments');
        }

        $department = Department::find($request->department_id);
        $department->update([
            'name' => $request->name,
            'type' => $request->type
        ]);

        app()->route->redirect('/departments');
    }

    public function delete(Request $request): void
    {
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/departments');
        }

        Department::find($request->department_id)->delete();
        app()->route->redirect('/departments');
    }

}