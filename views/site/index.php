<div class="container-card">
    <h2>Статистика по подразделениям</h2>
    <table class="table table-custom">
        <thead>
        <tr>
            <th>Подразделение</th>
            <th>Количество сотрудников</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($departmentStats as $stat): ?>
            <tr>
                <td><?= htmlspecialchars($stat['department']->name) ?></td>
                <td><?= $stat['user_count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="container-card">
    <h2>Статистика по помещениям</h2>
    <table class="table table-custom">
        <thead>
        <tr>
            <th>Помещение</th>
            <th>Количество телефонов</th>
            <th>Количество прикрепленных абонентов</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($roomStats as $stat): ?>
            <tr>
                <td><?= htmlspecialchars($stat['room']->number) ?></td>
                <td><?= $stat['phone_count'] ?></td>
                <td><?= $stat['user_count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>