<div class="container">
    <h1>Добавление нового телефона</h1>

    <form method="POST" action="/phones/store">
        <div class="form-group mb-3">
            <label for="number">Номер телефона:</label>
            <input type="number" class="form-control" id="number" name="number" required>
        </div>

        <div class="form-group mb-3">
            <label for="room_id">Помещение:</label>
            <select class="form-control" id="room_id" name="room_id" required>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room->room_id ?>">
                        <?= htmlspecialchars($room->name) ?> (<?= htmlspecialchars($room->type) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="user_id">Абонент:</label>
            <select class="form-control" id="user_id" name="user_id">
                <option value="">Не назначено</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user->id ?>">
                        <?= htmlspecialchars($user->surname) ?> <?= htmlspecialchars($user->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Добавить</button>
        <a href="/phones" class="btn btn-secondary">Отмена</a>
    </form>
</div>