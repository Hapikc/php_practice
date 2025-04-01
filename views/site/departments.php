<?php  use Src\Auth\Auth;
 ?>


<div class="container">
    <h1>Список подразделений</h1>

    <?php if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])): ?>
        <a href="/departments/create" class="btn btn-primary mb-3">Добавить подразделение</a>
    <?php endif; ?>

    <form method="GET" action="/departments" class="mb-4">
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
                <a href="/departments?sort=name&order=<?= $sort == 'name' && $order == 'asc' ? 'desc' : 'asc' ?>&search=<?= $search ?>">
                    Название <?= $sort == 'name' ? ($order == 'asc' ? '↑' : '↓') : '' ?>
                </a>
            </th>
            <th>
                <a href="/departments?sort=type&order=<?= $sort == 'type' && $order == 'asc' ? 'desc' : 'asc' ?>&search=<?= $search ?>">
                    Тип <?= $sort == 'type' ? ($order == 'asc' ? '↑' : '↓') : '' ?>
                </a>
            </th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($departments as $department): ?>
            <tr>
                <td><?= $department->name ?></td>
                <td><?= $department->type ?></td>
                <td>
                    <?php if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])): ?>
                        <a href="/departments/edit?department_id=<?= $department->department_id ?>" class="btn btn-sm btn-warning">Редактировать</a>
                        <form action="/departments/delete" method="POST" style="display: inline-block;">
                            <input type="hidden" name="department_id" value="<?= $department->department_id ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>