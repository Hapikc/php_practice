<?php
return [
    //Класс аутентификации
    'auth' => \Src\Auth\Auth::class,
    //Клас пользователя
    'identity' => \Model\User::class,
    //Классы для middleware
    'routeMiddleware' => [
        'auth' => \Middleware\AuthMiddleware::class,
        'admin' => \Middleware\AdminMiddleware::class,
        'adminOrSysadmin' => \Middleware\AdminOrSysadminMiddleware::class

    ],
    'validators' => [
        'required' => \Src\Validator\RequiredValidator::class,
        'min' => \Src\Validator\MinValidator::class,
        'unique' => \Src\Validator\UniqueValidator::class,
        'exists' => \Src\Validator\ExistsValidator::class,
        'email' => \Src\Validator\EmailValidator::class,
        'numeric' => \Src\Validator\NumericValidator::class,
    ]

];
