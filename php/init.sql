
-- Таблица пользователей
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fio VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  login VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица заявок
CREATE TABLE requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  weight INT NOT NULL,
  dimensions VARCHAR(100),
  from_address VARCHAR(255),
  to_address VARCHAR(255),
  cargo_type VARCHAR(100),
  status VARCHAR(50) DEFAULT 'Новая',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

