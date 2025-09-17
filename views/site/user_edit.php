<div class="container">
    <h1>Редактирование пользователя</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="form-group">
                <label>Текущая аватарка:</label>
                <div class="mt-2">
                    <img src="<?= $user->getAvatarUrl() ?>"
                         width="100"
                         height="100"
                         style="border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;"
                         alt="Аватар пользователя"
                         onerror="this.src='/public/images/default-avatar.png'">
                </div>
            </div>
        </div>
    </div>
    <form method="POST" action="/users/update" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $user->id ?>">
        <div class="form-group mb-4">
            <label for="avatar">Новая аватарка:</label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
            <small class="form-text text-muted">
                Разрешены форматы: JPG, PNG, GIF. Максимальный размер: 2MB
            </small>
        </div>

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
                           value="<?= htmlspecialchars($user->patronymic ?? '') ?>">
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
            <small class="form-text text-muted">Минимум 6 символов</small>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="role_id">Роль:</label>
                    <select class="form-control" id="role_id" name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->role_id ?>"
                                    <?= ($user->role_id == $role->role_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/users" class="btn btn-secondary">Отмена</a>

        <?php if ($user->avatar): ?>
            <button type="button" class="btn btn-outline-danger" onclick="confirmAvatarDelete()">
                Удалить аватарку
            </button>
        <?php endif; ?>
    </form>

    <?php if ($user->avatar): ?>
        <form id="deleteAvatarForm" method="POST" action="/users/delete-avatar" style="display: none;">
            <input type="hidden" name="user_id" value="<?= $user->id ?>">
        </form>
    <?php endif; ?>
</div>

<script>
    function confirmAvatarDelete() {
        if (confirm('Удалить аватарку пользователя?')) {
            document.getElementById('deleteAvatarForm').submit();
        }
    }
</script>