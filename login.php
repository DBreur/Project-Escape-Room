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

    $db_connection->exec(
        'ALTER TABLE users
            MODIFY id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            MODIFY username VARCHAR(255) NOT NULL,
            MODIFY password VARCHAR(255) NOT NULL'
    );

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

    if ($username === '' || $password === '') {
        $message = 'Vul zowel gebruikersnaam als wachtwoord in.';
    } else {
        $stmt = $db_connection->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        }

        $message = 'Onjuiste gebruikersnaam of wachtwoord.';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen - Escape Room</title>
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
    <h2>Inloggen</h2>
    <?php if ($message): ?>
      <p class="form-error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" class="auth-form">
        <label for="username">Gebruikersnaam</label>
      <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username ?? '') ?>">

      <label for="password">Wachtwoord</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Inloggen</button>
    </form>
    <p>Heb je nog geen account? <a href="register.php">Maak er een aan</a>.</p>
  </main>
</body>
</html>
