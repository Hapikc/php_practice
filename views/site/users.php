<div class="container-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Пользователи</h2>
        <?php if (app()->auth::check() && app()->auth::user()->role_id == 1): ?>
            <a href="/users/create" class="btn btn-primary btn-sm">+ Добавить</a>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Поиск..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>

            <div class="col-md-3">
                <select name="role_id" class="form-select">
                    <option value="">Все роли</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r->role_id ?>" <?= $selected_role == $r->role_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Фильтр</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover">
            <thead>
            <tr>
                <th>Аватар</th>
                <th>ФИО</th>
                <th>Логин</th>
                <th>Роль</th>
                <th width="120">Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?php if ($user->avatar): ?>
                            <img src="<?= $user->avatar ?>" alt="Аватар" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 40px; height: 40px; background: #eee; border-radius: 50%;"></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user->surname.' '.$user->name) ?></td>
                    <td><?= htmlspecialchars($user->login) ?></td>
                    <td><?= htmlspecialchars($user->role->name) ?></td>
                    <td>
                        <?php if (app()->auth::check() && app()->auth::user()->role_id == 1): ?>
                            <a href="/users/edit?user_id=<?= $user->id ?>" class="btn btn-sm btn-outline-secondary">✏️</a>
                            <form action="/users/delete" method="POST" style="display:inline">
                                <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить пользователя?')">🗑️</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>