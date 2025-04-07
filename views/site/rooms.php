<?php
use Src\Auth\Auth;
?>
<div class="container">
    <h1>Список помещений</h1>
    <?php if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])): ?>
        <a href="/rooms/create" class="btn btn-primary mb-3">Добавить помещение</a>
    <?php endif; ?>

    <form method="GET" action="/rooms" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Поиск..." value="<?= $search ?>">
                    <button type="submit" class="btn btn-outline-secondary">Найти</button>
                </div>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th>
                <a href="/rooms?sort=name&order=<?= $sort == 'name' && $order == 'asc' ? 'desc' : 'asc' ?>&search=<?= $search ?>">
                    Название <?= $sort == 'name' ? ($order == 'asc' ? '↑' : '↓') : '' ?>
                </a>
            </th>
            <th>
                <a href="/rooms?sort=type&order=<?= $sort == 'type' && $order == 'asc' ? 'desc' : 'asc' ?>&search=<?= $search ?>">
                    Тип <?= $sort == 'type' ? ($order == 'asc' ? '↑' : '↓') : '' ?>
                </a>
            </th>
            <th>Подразделение</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?= $room->name ?></td>
                <td><?= $room->type ?></td>
                <td><?= $room->department->name ?></td>
                <td>
                    <?php if (app()->auth::check() && in_array(app()->auth::user()->role_id, [1, 2])): ?>
                        <a href="/rooms/edit?room_id=<?= $room->room_id ?>" class="btn btn-sm btn-warning">✏️</a>
                        <form action="/rooms/delete" method="POST" style="display: inline-block;">
                            <input type="hidden" name="room_id" value="<?= $room->room_id ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">🗑️</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>