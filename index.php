<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escape Room</title>
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
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

  $teamName = null;
  if (!empty($_SESSION['user_id'])) {
      $teamStmt = $db_connection->prepare('SELECT t.team FROM teams t JOIN team_members tm ON t.id = tm.team_id WHERE tm.user_id = ? LIMIT 1');
      $teamStmt->execute([$_SESSION['user_id']]);
      $teamRow = $teamStmt->fetch(PDO::FETCH_ASSOC);
      $teamName = $teamRow ? $teamRow['team'] : null;
  }
  ?>

  <header class="site-header">
    <h1>Escape Room</h1>
    <div class="header-actions">
      <?php if (!empty($_SESSION['username'])): ?>
        <span>Welkom, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a class="header-button" href="teams.php">Teams</a>
        <a class="header-button" href="logout.php">Uitloggen</a>
      <?php else: ?>
        <a class="header-button" href="login.php">Inloggen</a>
        <a class="header-button header-button-alt" href="register.php">Account maken</a>
      <?php endif; ?>
    </div>
  </header>
  <main>
    <div class="content-box">
      <h1>Welkom</h1>
      <p>Je bent een 12-jarige jongen en zit opgesloten in de kelder van je stiefmoeders huis.
Om te ontsnappen, moet je eerst de code van de kluis vinden om de reservesleutel er uit te halen waarmee je de kelderdeur kunt openen.
Als je eenmaal boven bent, kom je in de hal van het huis. Daar ligt ergens de sleutel van de voordeur verstopt.
Lukt het jou om alle hints te vinden en op tijd uit het huis te ontsnappen?</p>

      <?php if (!empty($_SESSION['username'])): ?>
    <p>Je bent ingelogd als <strong><?= htmlspecialchars($_SESSION['username']) ?></strong><?php if ($teamName): ?> in team <strong><?= htmlspecialchars($teamName) ?></strong><?php endif; ?>.</p>
    <a class="header-button" href="teams.php">Bekijk teams</a>
  <?php else: ?>
    <p>Log in om een team te kiezen en mee te spelen.</p>
  <?php endif; ?>

  <button><a href="./rooms/room_1.php">Klik hier om de escape te starten </a></button>
    </div>
</main>

<footer>
  <p>&copy; 2024 Escape Room. All rights reserved.</p>
</footer>
</body>

</html>