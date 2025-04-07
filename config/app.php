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
        'adminOrSysadmin' => \Middleware\AdminOrSysadminMiddleware::class,
        'csrf' => \Middleware\CSRFMiddleware::class,
        'trim' => \Middleware\TrimMiddleware::class,
        'specialChars' => \Middleware\SpecialCharsMiddleware::class,


    ],
    'validators' => [
        'required' => \Validators\RequireValidator::class,
        'unique' => \Validators\UniqueValidator::class
    ]

];
