<div class="container">
    <h1>Редактирование пользователя</h1>

    <form method="POST" action="/users/update">
        <input type="hidden" name="user_id" value="<?= $user->id ?>">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="surname">Фамилия:</label>
                    <input type="text" class="form-control" id="surname" name="surname"
                           value="<?= htmlspecialchars($user->surname) ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="name">Имя:</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= htmlspecialchars($user->name) ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="patronymic">Отчество:</label>
                    <input type="text" class="form-control" id="patronymic" name="patronymic"
                           value="<?= htmlspecialchars($user->patronymic) ?>">
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="login">Логин:</label>
            <input type="text" class="form-control" id="login" name="login"
                   value="<?= htmlspecialchars($user->login) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="password">Новый пароль (оставьте пустым, чтобы не менять):</label>
            <input type="password" class="form-control" id="password" name="password" minlength="6">
        </div>


            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="role_id">Роль:</label>
                    <select class="form-control" id="role_id" name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->role_id ?>"
                                <?= $role->role_id == $user->role_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/users" class="btn btn-secondary">Отмена</a>
    </form>
</div>