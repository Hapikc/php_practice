<div class="container-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Список подразделений</h2>
        <?php if (app()->auth::check() && in_array(app()->auth::user()->role_id, [1, 2])): ?>
            <a href="/departments/create" class="btn btn-primary btn-sm">+ Добавить</a>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Поиск..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Найти</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover">
            <thead>
            <tr>
                <th>Название</th>
                <th>Тип</th>
                <th width="150">Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($departments as $department): ?>
                <tr>
                    <td><?= htmlspecialchars($department->name) ?></td>
                    <td><?= htmlspecialchars($department->type) ?></td>
                    <td>
                        <?php if (app()->auth::check() && in_array(app()->auth::user()->role_id, [1, 2])): ?>
                            <a href="/departments/edit?department_id=<?= $department->department_id ?>" class="btn btn-sm btn-outline-secondary">✏️</a>
                            <form action="/departments/delete" method="POST" style="display:inline">
                                <input type="hidden" name="department_id" value="<?= $department->department_id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить подразделение?')">🗑️</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>