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

$userId = $_SESSION['user_id'];
$message = '';
$action = $_POST['action'] ?? '';
$targetTeam = isset($_POST['team_id']) ? (int) $_POST['team_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'join' && $targetTeam > 0) {
        $delete = $db_connection->prepare('DELETE FROM team_members WHERE user_id = ?');
        $delete->execute([$userId]);

        $insert = $db_connection->prepare('INSERT IGNORE INTO team_members (team_id, user_id) VALUES (?, ?)');
        $insert->execute([$targetTeam, $userId]);
        $message = 'Je zit nu in het team.';
    }

    if ($action === 'leave') {
        $delete = $db_connection->prepare('DELETE FROM team_members WHERE user_id = ?');
        $delete->execute([$userId]);
        $message = 'Je hebt het team verlaten.';
    }
}

$currentTeamStmt = $db_connection->prepare(
    'SELECT t.id, t.team FROM teams t JOIN team_members tm ON t.id = tm.team_id WHERE tm.user_id = ? LIMIT 1'
);
$currentTeamStmt->execute([$userId]);
$currentTeam = $currentTeamStmt->fetch(PDO::FETCH_ASSOC);

$teamsStmt = $db_connection->query(
    'SELECT t.id, t.team, COUNT(tm.id) AS members FROM teams t LEFT JOIN team_members tm ON t.id = tm.team_id GROUP BY t.id ORDER BY t.team'
);
$teams = $teamsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teams - Escape Room</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <header class="site-header">
    <h1>Escape Room</h1>
    <div class="header-actions">
      <span>Ingelogd als <?= htmlspecialchars($_SESSION['username']) ?></span>
      <a class="header-button" href="index.php">Home</a>
      <a class="header-button" href="teams_overview.php">Teams & leden</a>
      <a class="header-button header-button-alt" href="create_team.php">Nieuw team</a>
      <a class="header-button" href="logout.php">Uitloggen</a>
    </div>
  </header>

  <main>
    <div class="content-box">
      <h2>Teams</h2>
      <?php if ($message): ?>
        <p class="form-error"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <div class="team-status">
      <?php if ($currentTeam): ?>
        <p>Je zit momenteel in team <strong><?= htmlspecialchars($currentTeam['team']) ?></strong>.</p>
        <form method="post">
          <input type="hidden" name="action" value="leave">
          <button type="submit">Team verlaten</button>
        </form>
      <?php else: ?>
        <p>Je zit nog in geen team. Kies hieronder een team om te joinen.</p>
      <?php endif; ?>
      </div>

      <div class="team-grid">
      <?php foreach ($teams as $team): ?>
        <div class="team-card">
          <h3><?= htmlspecialchars($team['team']) ?></h3>
          <p>Leden: <?= htmlspecialchars($team['members']) ?></p>
          <?php if ($currentTeam && $currentTeam['id'] === (int) $team['id']): ?>
            <button disabled>Je bent hier lid van</button>
          <?php else: ?>
            <form method="post">
              <input type="hidden" name="action" value="join">
              <input type="hidden" name="team_id" value="<?= (int) $team['id'] ?>">
              <button type="submit">Join</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

      <?php if (empty($teams)): ?>
        <p>Er zijn nog geen teams. Maak er een aan via de knop hierboven.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
