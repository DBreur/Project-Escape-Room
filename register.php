<?php
session_start();
require 'dbcon.php';

function ensureUsersTable(PDO $db_connection) {
    $db_connection->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    );

    $existingColumns = $db_connection->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('user_id', $existingColumns, true)) {
        $db_connection->exec('ALTER TABLE users DROP COLUMN user_id');
    }

    try {
        $db_connection->exec('ALTER TABLE users MODIFY id INT NOT NULL AUTO_INCREMENT');
    } catch (PDOException $ignored) {
        // ignore if column already has desired type
    }

    try {
        $db_connection->exec('ALTER TABLE users ADD PRIMARY KEY (id)');
    } catch (PDOException $ignored) {
        // ignore if primary key already exists
    }

    $db_connection->exec('ALTER TABLE users MODIFY username VARCHAR(255) NOT NULL');
    $db_connection->exec('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');

    try {
        $db_connection->exec('ALTER TABLE users ADD UNIQUE KEY idx_users_username (username)');
    } catch (PDOException $ignored) {
        // unique key may already exist
    }
}

ensureUsersTable($db_connection);

$message = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $message = 'Vul alle velden in.';
    } elseif ($password !== $confirm) {
        $message = 'Wachtwoorden komen niet overeen.';
    } else {
        $stmt = $db_connection->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $message = 'Deze gebruikersnaam is al in gebruik.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $db_connection->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $insert->execute([$username, $passwordHash]);

            session_regenerate_id(true);
            $_SESSION['user_id'] = $db_connection->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account maken - Escape Room</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <header class="site-header">
    <h1>Escape Room</h1>
    <div class="header-actions">
      <?php if (!empty($_SESSION['username'])): ?>
        <span>Ingelogd als <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a class="header-button" href="logout.php">Uitloggen</a>
      <?php else: ?>
        <a class="header-button" href="login.php">Inloggen</a>
        <a class="header-button header-button-alt" href="register.php">Account maken</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <h2>Account maken</h2>
    <?php if ($message): ?>
      <p class="form-error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" class="auth-form">
      <label for="username">Gebruikersnaam</label>
      <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username ?? '') ?>">

      <label for="password">Wachtwoord</label>
      <input type="password" id="password" name="password" required>

      <label for="confirm_password">Herhaal wachtwoord</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit">Account maken</button>
    </form>
    <p>Heb je al een account? <a href="login.php">Log in</a>.</p>
  </main>
</body>
</html>
