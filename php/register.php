<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fio = trim($_POST['fio'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fio === '' || $phone === '' || $email === '' || $login === '' || $password === '') {
        $error = 'Пожалуйста, заполните все поля.';
    } else {
        // Проверка на существующий логин
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $error = 'Такой логин уже существует.';
        } else {
            // Хешируем пароль
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Вставка пользователя
            $stmt = $pdo->prepare("INSERT INTO users (fio, phone, email, login, password) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$fio, $phone, $email, $login, $hashedPassword])) {
                // Успешно — перенаправляем на login
                header('Location: login.php');
                exit;
            } else {
                $error = 'Ошибка при регистрации. Попробуйте позже.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Регистрация | Грузовозофф</title>
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
      <a href="index.html" class="btn btn-outline-primary">На главную</a>
    </div>
  </header>

  <main>
    <div class="form-container">
      <h3 class="text-center mb-4">Регистрация</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">ФИО</label>
          <input type="text" class="form-control" name="fio" required value="<?=htmlspecialchars($_POST['fio'] ?? '')?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Телефон</label>
          <input type="text" class="form-control" name="phone" placeholder="+7(900)-123-45-67" required value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Логин</label>
          <input type="text" class="form-control" name="login" required value="<?=htmlspecialchars($_POST['login'] ?? '')?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Пароль</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
      </form>
    </div>
  </main>

  <footer class="footer">
    &copy; 2025 Грузовозофф. Все права защищены.
  </footer>

</body>
</html>
