<div class="container">
    <h2>Статистика по подразделениям</h2>

    <table class="table">
        <thead>
        <tr>
            <th>Подразделение</th>
            <th>Количество абонентов</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stats as $stat): ?>
            <tr>
                <td><?= $stat['name'] ?></td>
                <td><?= $stat['users_count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/phones" class="btn btn-secondary">Назад к списку</a>
</div>