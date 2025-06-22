<?php
session_start();
require_once 'config.php'; // Подключение к БД

// Выход из сессии
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Защита: неавторизованных — на login.php
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$fio = $_SESSION['fio'];

// Обработка формы заявки
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $weight = $_POST['weight'];
    $dimensions = $_POST['dimensions'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $type = $_POST['type'];
    $status = 'Новая';

    $stmt = $pdo->prepare("INSERT INTO requests (user_id, date, time, weight, dimensions, from_address, to_address, cargo_type, status)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $date, $time, $weight, $dimensions, $from, $to, $type, $status]);
}

// Получение заявок текущего пользователя
$stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Личный кабинет — Грузовозофф</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f0f2f5, #e0e7ff);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .container-box {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      margin-top: 40px;
      margin-bottom: 40px;
    }

    h2, h4 {
      font-weight: bold;
      color: #343a40;
    }

    footer {
      background-color: #343a40;
      color: white;
      padding: 15px 0;
      text-align: center;
      margin-top: auto;
    }

    .table thead {
      background-color: #f8f9fa;
    }

    .form-label {
      font-weight: 500;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <div class="container container-box">
    <div class="top-bar">
      <h2>Личный кабинет</h2>
      <div>
        <span class="me-3">Вы вошли как: <strong><?= htmlspecialchars($fio) ?></strong></span>
        <a href="?action=logout" class="btn btn-outline-danger btn-sm">Выйти</a>
      </div>
    </div>

    <h4>Новая заявка на перевозку</h4>
    <form class="mb-5" method="post">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="date" class="form-label">Дата перевозки</label>
          <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="time" class="form-label">Время перевозки</label>
          <input type="time" name="time" id="time" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="weight" class="form-label">Вес груза (кг)</label>
          <input type="number" name="weight" id="weight" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="dimensions" class="form-label">Габариты (ДxШxВ см)</label>
          <input type="text" name="dimensions" id="dimensions" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="from" class="form-label">Адрес отправления</label>
          <input type="text" name="from" id="from" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="to" class="form-label">Адрес доставки</label>
          <input type="text" name="to" id="to" class="form-control" required>
        </div>
        <div class="col-md-12 mb-3">
          <label for="type" class="form-label">Тип груза</label>
          <select name="type" id="type" class="form-select" required>
            <option value="">Выберите тип</option>
            <option>Хрупкое</option>
            <option>Скоропортящееся</option>
            <option>Требуется рефрижератор</option>
            <option>Животные</option>
            <option>Жидкость</option>
            <option>Мебель</option>
            <option>Мусор</option>
          </select>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary w-100">Отправить заявку</button>
        </div>
      </div>
    </form>

    <h4>Ваши заявки</h4>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>Дата</th>
          <th>Время</th>
          <th>Адрес от</th>
          <th>Адрес до</th>
          <th>Вес</th>
          <th>Тип</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $req): ?>
          <tr>
            <td><?= htmlspecialchars($req['date']) ?></td>
            <td><?= htmlspecialchars($req['time']) ?></td>
            <td><?= htmlspecialchars($req['from_address']) ?></td>
            <td><?= htmlspecialchars($req['to_address']) ?></td>
            <td><?= htmlspecialchars($req['weight']) ?></td>
            <td><?= htmlspecialchars($req['cargo_type']) ?></td>
            <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($req['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <footer>
    &copy; 2025 Грузовозофф. Все права защищены.
  </footer>

</body>
</html>
