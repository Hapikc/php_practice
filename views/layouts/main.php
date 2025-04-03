<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Phone System</title>
    <link href="../../public/css/main.css" rel="stylesheet">
    <style>

    </style>
</head>
<body>
<header>
    <div class="container">
        <nav class="d-flex align-items-center">
            <a href="<?= app()->route->getUrl('/hello') ?>" class="me-3 fw-bold">Главная</a>
            <?php if (app()->auth::check() && (app()->auth::user()->isAdmin() || app()->auth::user()->isSysadmin())): ?>
                <a href="<?= app()->route->getUrl('/users') ?>" class="me-3">Пользователи</a>
                <a href="<?= app()->route->getUrl('/phones') ?>" class="me-3">Телефоны</a>
                <a href="<?= app()->route->getUrl('/rooms') ?>" class="me-3">Помещения</a>
                <a href="<?= app()->route->getUrl('/departments') ?>" class="me-3">Подразделения</a>
            <?php endif; ?>

            <?php if (!app()->auth::check()): ?>
                <a href="<?= app()->route->getUrl('/login') ?>" class="me-3">Вход</a>
                <a href="<?= app()->route->getUrl('/signup') ?>">Регистрация</a>
            <?php else: ?>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-white me-3"><?= htmlspecialchars(app()->auth::user()->name) ?></span>
                    <a href="<?= app()->route->getUrl('/logout') ?>" class="btn btn-sm btn-outline-light">Выход</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <?= $content ?? '' ?>
    </div>
</main>

</body>
</html>