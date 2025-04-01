<?php use Src\Auth\Auth;
 ?>

<div class="container">
    <h1>Список пользователей</h1>

    <?php if (Auth::check() && Auth::user()->role_id == 1): ?>
        <a href="/users/create" class="btn btn-primary mb-3">Добавить пользователя</a>
    <?php endif; ?>

    <form method="GET" action="/users" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Поиск по ФИО или логину..."
                       value="<?= htmlspecialchars($search ?? '') ?>">
            </div>

            <div class="col-md-3">
                <select name="department_id" class="form-select">
                    <option value="">Все подразделения</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department->department_id ?>"
                            <?= $selected_department == $department->department_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="role_id" class="form-select">
                    <option value="">Все роли</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->role_id ?>"
                            <?= $selected_role == $role->role_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Фильтровать</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th>
                <a href="/users?sort=surname&order=<?= $sort == 'surname' && $order == 'asc' ? 'desc' : 'asc' ?>&search=<?= $search ?? '' ?>">
                    Фамилия <?= $sort == 'surname' ? ($order == 'asc' ? '↑' : '↓') : '' ?>
                </a>
            </th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Логин</th>
            <th>Подразделение</th>
            <th>Роль</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user->surname) ?></td>
                <td><?= htmlspecialchars($user->name) ?></td>
                <td><?= htmlspecialchars($user->patronymic) ?></td>
                <td><?= htmlspecialchars($user->login) ?></td>
                <td><?= htmlspecialchars($user->department->name) ?></td>
                <td><?= htmlspecialchars($user->role->name) ?></td>
                <td>
                    <?php if (Auth::check() && Auth::user()->role_id == 1): ?>
                        <a href="/users/edit?user_id=<?= $user->id ?>" class="btn btn-sm btn-warning">Редактировать</a>
                        <form action="/users/delete" method="POST" style="display: inline-block;">
                            <input type="hidden" name="user_id" value="<?= $user->id ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>