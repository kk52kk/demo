<?php
session_start();

// Логаут
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Полностью уничтожаем сессию
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Перенаправляем на главную страницу
    header('Location: index.html');
    exit;
}

// Простая проверка логина и пароля администратора
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($login === 'admin' && $password === 'gruzovik2024') {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = 'Неверный логин или пароль администратора.';
        }
    } else {
        // Если не авторизован и форма не отправлена, показываем форму входа
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Вход в админку — Грузовозофф</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f9fa;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .login-form {
                    background: white;
                    padding: 30px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    width: 320px;
                }
            </style>
        </head>
        <body>
        <form method="post" class="login-form">
            <h3 class="mb-3 text-center">Вход в админку</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" name="login" id="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
        </body>
        </html>
        <?php
        exit;
    }
}

// Подключаем БД
require_once 'config.php';

// Обработка POST-запросов: обновление статуса или удаление заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_id'], $_POST['status'])) {
        // Обновление статуса заявки
        $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['request_id']]);
    } elseif (isset($_POST['delete_request_id'])) {
        // Удаление заявки
        $stmt = $pdo->prepare("DELETE FROM requests WHERE id = ?");
        $stmt->execute([$_POST['delete_request_id']]);
    }
}

// Получение всех заявок с данными пользователей
$stmt = $pdo->query("SELECT r.*, u.fio FROM requests r JOIN users u ON r.user_id = u.id ORDER BY r.id DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Админ-панель — Грузовозофф</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f2f5;
      padding: 40px;
    }
    h2 {
      margin-bottom: 30px;
    }
    table th, table td {
      vertical-align: middle;
    }
  </style>
  <script>
    function confirmDelete() {
      return confirm('Вы уверены, что хотите удалить эту заявку?');
    }
  </script>
</head>
<body>

  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Админ-панель — Все заявки</h2>
      <a href="admin.php?action=logout" class="btn btn-danger">Выйти из админки</a>
    </div>

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Пользователь</th>
          <th>Дата</th>
          <th>Время</th>
          <th>Адрес от</th>
          <th>Адрес до</th>
          <th>Вес</th>
          <th>Тип</th>
          <th>Статус</th>
          <th>Действие</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $req): ?>
          <tr>
            <td><?= htmlspecialchars($req['fio']) ?></td>
            <td><?= htmlspecialchars($req['date']) ?></td>
            <td><?= htmlspecialchars($req['time']) ?></td>
            <td><?= htmlspecialchars($req['from_address']) ?></td>
            <td><?= htmlspecialchars($req['to_address']) ?></td>
            <td><?= htmlspecialchars($req['weight']) ?></td>
            <td><?= htmlspecialchars($req['cargo_type']) ?></td>
            <td>
              <span class="badge bg-secondary"><?= htmlspecialchars($req['status']) ?></span>
            </td>
            <td>
              <form method="post" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                <select name="status" class="form-select form-select-sm" style="width: 120px;">
                  <option value="Новая" <?= $req['status'] == 'Новая' ? 'selected' : '' ?>>Новая</option>
                  <option value="В пути" <?= $req['status'] == 'В пути' ? 'selected' : '' ?>>В пути</option>
                  <option value="Доставлено" <?= $req['status'] == 'Доставлено' ? 'selected' : '' ?>>Доставлено</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Обновить</button>
              </form>

              <form method="post" onsubmit="return confirmDelete();" style="display:inline-block; margin-left:5px;">
                <input type="hidden" name="delete_request_id" value="<?= $req['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Удалить заявку</button>
              </form>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>

</body>
</html>
