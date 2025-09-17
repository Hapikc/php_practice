<div class="container-card">
    <h3 class="mb-4"><?= isset($department) ? 'Редактирование подразделения' : 'Новое подразделение' ?></h3>

    <form method="POST" action="<?= isset($department) ? '/departments/update' : '/departments/store' ?>">
        <?php if (isset($department)): ?>
            <input type="hidden" name="department_id" value="<?= $department->department_id ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($department->name ?? '') ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Тип</label>
            <input type="text" class="form-control" name="type" value="<?= htmlspecialchars($department->type ?? '') ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/departments" class="btn btn-link">Отмена</a>
        </div>
    </form>
</div>