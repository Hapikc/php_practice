<?php
//Включаем запрет на неявное преобразование типов
declare(strict_types=1);
//Включаем сессии на все страницы
session_start();


try {
    //Создаем экземпляр приложения и запускаем его
    $app = require_once __DIR__ . '/../core/bootstrap.php';
    $app->run();
} catch (\Throwable $exception) {
    echo '<pre>';
    print_r($exception);
    echo '</pre>';
}

try {
    require_once __DIR__ . '/../route/web.php';
    $app = new Src\Application(new Src\Settings(getConfigs()));
    $app->run();
} catch (\Exception $e) {
    if ($e->getCode() === 403) {
        http_response_code(403);
        echo "Доступ запрещен";
    } else {
        http_response_code(500);
        echo "Произошла ошибка: " . $e->getMessage();
    }
}