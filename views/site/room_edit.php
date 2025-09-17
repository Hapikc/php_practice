<div class="container">
    <h1>Редактирование помещения</h1>

    <form method="POST" action="/rooms/update">
        <input type="hidden" name="room_id" value="<?= $room->room_id ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Название помещения</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?= $room->name ?>" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Тип помещения</label>
            <input type="text" class="form-control" id="type" name="type"
                   value="<?= $room->type ?>" required>
        </div>

        <div class="mb-3">
            <label for="department_id" class="form-label">Подразделение</label>
            <select class="form-select" id="department_id" name="department_id" required>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department->department_id ?>"
                        <?= $department->department_id == $room->department_id ? 'selected' : '' ?>>
                        <?= $department->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/rooms" class="btn btn-secondary">Отмена</a>
    </form>
</div>