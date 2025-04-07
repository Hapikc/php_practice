<div class="container-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Телефоны</h2>
        <div>
            <a href="/phones/create" class="btn btn-primary btn-sm">+ Добавить</a>
            <a href="/phones/by-room" class="btn btn-secondary btn-sm">По помещениям</a>

            <a href="/phones/by-user" class="btn btn-secondary btn-sm">По абонентам</a>
            <a href="/phones/count-by-department" class="btn btn-info btn-sm">Статистика по подразделениям</a>
            <a href="/phones/count-by-room" class="btn btn-info btn-sm">Статистика по помещениям</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover">
            <thead>
            <tr>
                <th>Номер</th>
                <th>Помещение</th>
                <th>Абонент</th>
                <th>Подразделение</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($phones as $phone): ?>
                <tr>
                    <td><?= htmlspecialchars($phone->number) ?></td>
                    <td><?= htmlspecialchars($phone->room->name) ?></td>
                    <td><?= $phone->user ? htmlspecialchars($phone->user->surname.' '.$phone->user->name) : '-' ?></td>
                    <td><?= htmlspecialchars($phone->room->department->name) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>