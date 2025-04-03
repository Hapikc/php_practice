<div class="container-card" style="max-width: 400px; margin: 0 auto">
    <h2 class="h4 mb-4">Авторизация</h2>

    <?php if (!app()->auth::check()): ?>
        <form method="post">
            <div class="mb-3">
                <input type="text" class="form-control" name="login" placeholder="Логин" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control" name="password" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Вы уже авторизованы как <?= htmlspecialchars(app()->auth::user()->name) ?>
        </div>
    <?php endif; ?>
</div>