<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'Пожалуйста, заполните все поля.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Успешная авторизация
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fio'] = $user['fio'];
            $_SESSION['is_admin'] = $user['is_admin'];

            header('Location: lk.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход | Грузовозофф</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .header {
      background-color: white;
      color: #212529;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #dee2e6;
    }
    main {
      flex: 1;
    }
    .form-container {
      max-width: 500px;
      margin: 40px auto;
      padding: 30px;
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 15px;
    }
    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }
    .btn-primary:hover {
      background-color: #0b5ed7;
      border-color: #0a58ca;
    }
  </style>
</head>
<body>

 <header class="header">
  <h2 class="m-0">Грузовозофф</h2>
  <div>
    <a href="index.html" class="btn btn-outline-primary me-2">На главную</a>
    <a href="admin.php" class="btn btn-outline-secondary me-2">Администратор</a>
  </div>
</header>

  <main>
    <div class="form-container">
      <h3 class="text-center mb-4">Вход</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Логин</label>
          <input type="text" class="form-control" name="login" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Пароль</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Войти</button>
      </form>
    </div>
  </main>

  <footer class="footer">
    &copy; 2025 Грузовозофф. Все права защищены.
  </footer>

</body>
</html>
