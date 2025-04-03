<div class="container">
    <h1>Телефоны абонентов</h1>

    <form method="GET" action="/phones/by-user" class="mb-4">
        <div class="form-group">
            <label for="user_id">Выберите абонента:</label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Все абоненты</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user->id ?>"
                        <?= $selectedUser == $user->id ? 'selected' : '' ?>>
                        <?= $user->surname ?> <?= $user->name ?>
                        (<?= $user->department->name ?? 'Не указано' ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Фильтровать</button>
    </form>

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
    <a href="/phones" class="btn btn-secondary">Назад к списку</a>
</div>