<?php

use Src\Route;

// Основные маршруты
Route::add('GET', '/hello', [Controller\Site::class, 'hello'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/signup', [Controller\Site::class, 'signup']);
Route::add(['GET', 'POST'], '/login', [Controller\Site::class, 'login']);
Route::add('GET', '/logout', [Controller\Site::class, 'logout']);

// Маршруты для телефонов
Route::add('GET', '/phones', [Controller\PhoneController::class, 'index'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/phones/create', [Controller\PhoneController::class, 'create'])
    ->middleware('auth', 'adminOrSysadmin');
Route::add('GET', '/phones/by-department', [Controller\PhoneController::class, 'byDepartment'])
    ->middleware('auth');
Route::add('GET', '/phones/by-room', [Controller\PhoneController::class, 'byRoom'])
    ->middleware('auth');

// Маршруты для помещений
Route::add('GET', '/rooms', [Controller\RoomController::class, 'index'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/rooms/create', [Controller\RoomController::class, 'create'])
    ->middleware('auth', 'adminOrSysadmin');
Route::add(['GET', 'POST'], '/rooms/edit', [Controller\RoomController::class, 'edit'])
    ->middleware('auth', 'adminOrSysadmin');
Route::add('POST', '/rooms/delete', [Controller\RoomController::class, 'delete'])
    ->middleware('auth', 'adminOrSysadmin');

// Маршруты для подразделений
Route::add('GET', '/departments', [Controller\DepartmentController::class, 'index'])
    ->middleware('auth');
Route::add(['GET', 'POST'], '/departments/create', [Controller\DepartmentController::class, 'create'])
    ->middleware('auth', 'adminOrSysadmin');
Route::add(['GET', 'POST'], '/departments/edit', [Controller\DepartmentController::class, 'edit'])
    ->middleware('auth', 'adminOrSysadmin');
Route::add('POST', '/departments/delete', [Controller\DepartmentController::class, 'delete'])
    ->middleware('auth', 'adminOrSysadmin');

// Маршруты для пользователей
Route::add('GET', '/users', [Controller\UserController::class, 'index'])
    ->middleware('auth', 'adminOrSysadmin');
Route::add(['GET', 'POST'], '/users/create', [Controller\UserController::class, 'create'])
    ->middleware('auth', 'admin');
Route::add(['GET', 'POST'], '/users/edit', [Controller\UserController::class, 'edit'])
    ->middleware('auth', 'admin');
Route::add('POST', '/users/delete', [Controller\UserController::class, 'delete'])
    ->middleware('auth', 'admin');