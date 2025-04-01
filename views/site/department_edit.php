<?php require __DIR__ . '/../layouts/main.php'; ?>

<div class="container">
    <h1>Редактирование подразделения</h1>

    <form method="POST" action="/departments/update">
        <input type="hidden" name="department_id" value="<?= $department->department_id ?>">

        <div class="form-group mb-3">
            <label for="name">Название:</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?= $department->name ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="type">Тип:</label>
            <input type="text" class="form-control" id="type" name="type"
                   value="<?= $department->type ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="/departments" class="btn btn-secondary">Отмена</a>
    </form>
</div>