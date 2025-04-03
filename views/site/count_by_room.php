<div class="container">
    <h2>Количество телефонов (абонентов) по помещениям</h2>

    <table class="table">
        <thead>
        <tr>
            <th>Помещение</th>
            <th>Количество телефонов</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stats as $room): ?>
            <tr>
                <td><?= $room->name ?></td>
                <td><?= $room->phones_count ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>