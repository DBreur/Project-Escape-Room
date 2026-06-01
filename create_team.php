<?php
session_start();
require 'dbcon.php';

function ensureTeamTables(PDO $db_connection) {
    $db_connection->exec(
        "CREATE TABLE IF NOT EXISTS teams (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            team VARCHAR(255) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    );

    $db_connection->exec(
        "CREATE TABLE IF NOT EXISTS team_members (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            team_id INT NOT NULL,
            user_id INT NOT NULL,
            UNIQUE KEY idx_team_member (team_id, user_id),
            KEY idx_team_members_team (team_id),
            KEY idx_team_members_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    );
}

ensureTeamTables($db_connection);

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');

    if ($teamName === '') {
        $message = 'Voer een teamnaam in.';
    } else {
        $stmt = $db_connection->prepare('SELECT id FROM teams WHERE team = ? LIMIT 1');
        $stmt->execute([$teamName]);

        if ($stmt->fetch()) {
            $message = 'Deze teamnaam bestaat al.';
        } else {
            $insert = $db_connection->prepare('INSERT INTO teams (team) VALUES (?)');
            $insert->execute([$teamName]);
            $teamId = $db_connection->lastInsertId();

            $delete = $db_connection->prepare('DELETE FROM team_members WHERE user_id = ?');
            $delete->execute([$_SESSION['user_id']]);

            $join = $db_connection->prepare('INSERT INTO team_members (team_id, user_id) VALUES (?, ?)');
            $join->execute([$teamId, $_SESSION['user_id']]);

            header('Location: teams.php');
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
  <title>Nieuw team maken - Escape Room</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <header class="site-header">
    <h1>Escape Room</h1>
    <div class="header-actions">
      <span>Ingelogd als <?= htmlspecialchars($_SESSION['username']) ?></span>
      <a class="header-button" href="teams.php">Teams</a>
      <a class="header-button" href="index.php">Home</a>
      <a class="header-button" href="logout.php">Uitloggen</a>
    </div>
  </header>

  <main>
    <div class="content-box">
      <h2>Maak een nieuw team</h2>
      <?php if ($message): ?>
        <p class="form-error"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <form method="post" class="auth-form">
      <label for="team_name">Teamnaam</label>
      <input type="text" id="team_name" name="team_name" required>
      <button type="submit">Team maken en joinen</button>
      </form>
    </div>
  </main>
</body>
</html>
