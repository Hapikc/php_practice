

<div class="container">
    <h1>Список телефонов</h1>
    <table class="table">
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
                <td><?= $phone->number ?></td>
                <td><?= $phone->room->name ?></td>
                <td><?= $phone->user ? $phone->user->surname . ' ' . $phone->user->name : 'Не назначен' ?></td>
                <td><?= $phone->room->department->name ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/phones/create" class="btn btn-primary">Добавить телефон</a>
    <a href="/phones/by-department" class="btn btn-secondary">По подразделениям</a>
    <a href="/phones/by-room" class="btn btn-info">По комнатам</a>
</div>