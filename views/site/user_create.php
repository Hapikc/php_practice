<div class="container">
    <h1>Добавление нового пользователя</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/users/store" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="avatar">Аватарка:</label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="surname">Фамилия:</label>
                    <input type="text" class="form-control" id="surname" name="surname"
                           value="<?= isset($old['surname']) ? htmlspecialchars($old['surname']) : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="name">Имя:</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= isset($old['name']) ? htmlspecialchars($old['name']) : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="patronymic">Отчество:</label>
                    <input type="text" class="form-control" id="patronymic" name="patronymic"
                           value="<?= isset($old['patronymic']) ? htmlspecialchars($old['patronymic']) : '' ?>">
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="login">Логин:</label>
            <input type="text" class="form-control" id="login" name="login"
                   value="<?= isset($old['login']) ? htmlspecialchars($old['login']) : '' ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="password">Пароль:</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="6">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="role_id">Роль:</label>
                    <select class="form-control" id="role_id" name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->role_id ?>"
                                    <?= (isset($old['role_id']) && $old['role_id'] == $role->role_id) ? 'selected' : '' ?>>
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