<?php require __DIR__ . '/../layouts/main.php'; ?>

<div class="container">
    <h1>Добавление нового подразделения</h1>

    <form method="POST" action="/departments/store">
        <div class="mb-3">
            <label for="name" class="form-label">Название подразделения</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Тип подразделения</label>
            <input type="text" class="form-control" id="type" name="type" required>
        </div>

        <button type="submit" class="btn btn-primary">Создать</button>
        <a href="/departments" class="btn btn-secondary">Отмена</a>
    </form>
</div>