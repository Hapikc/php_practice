<div class="container">
    <h1>Телефоны по помещениям</h1>

    <form method="GET" action="/phones/by-room" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="department_id">Подразделение:</label>
                    <select name="department_id" id="department_id" class="form-control">
                        <option value="">Все подразделения</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department->department_id ?>"
                                <?= $selectedDepartment == $department->department_id ? 'selected' : '' ?>>
                                <?= $department->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="room_id">Помещение:</label>
                    <select name="room_id" id="room_id" class="form-control">
                        <option value="">Все помещения</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= $room->room_id ?>"
                                <?= $selectedRoom == $room->room_id ? 'selected' : '' ?>>
                                <?= $room->name ?> (<?= $room->type ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Фильтровать</button>
        <a href="/phones/by-room" class="btn btn-outline-secondary">Сбросить</a>
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