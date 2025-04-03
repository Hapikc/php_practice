<div class="container">
    <h1>Добавление нового помещения</h1>

    <form method="POST" action="/rooms/store">
        <div class="form-group mb-3">
            <label for="name">Название помещения:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group mb-3">
            <label for="type">Тип помещения:</label>
            <input type="text" class="form-control" id="type" name="type" required>
        </div>

        <div class="form-group mb-3">
            <label for="department_id">Подразделение:</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department->department_id ?>"><?= $department->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/rooms" class="btn btn-secondary">Отмена</a>
    </form>
</div>