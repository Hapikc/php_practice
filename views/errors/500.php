<!DOCTYPE html>
<html>
<head>
    <title>Ошибка 500</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #d9534f; }
    </style>
</head>
<body>
<h1>Ошибка 500</h1>
<p>Внутренняя ошибка сервера</p>
<?php if (isset($message)): ?>
    <p><strong>Детали:</strong> <?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<a href="/hello">Вернуться на главную</a>
</body>
</html>