<div class="container">
    <h1>405 Method Not Allowed</h1>
    <div class="alert alert-danger">
        <p>Использован неподдерживаемый метод: <strong><?= htmlspecialchars($requestedMethod ?? '') ?></strong></p>
        <p>Разрешенные методы: <strong><?= htmlspecialchars($allowedMethods ?? '') ?></strong></p>
    </div>
    <a href="/" class="btn btn-primary">На главную</a>
</div>