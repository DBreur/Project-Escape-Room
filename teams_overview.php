<?php
session_start();
require 'dbcon.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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

$teamsStmt = $db_connection->query(
    'SELECT t.id, t.team, u.username
     FROM teams t
     LEFT JOIN team_members tm ON tm.team_id = t.id
     LEFT JOIN users u ON u.id = tm.user_id
     ORDER BY t.team, u.username'
);
$rows = $teamsStmt->fetchAll(PDO::FETCH_ASSOC);

$teams = [];
foreach ($rows as $row) {
    $teamId = $row['id'];
    if (!isset($teams[$teamId])) {
        $teams[$teamId] = [
            'name' => $row['team'],
            'members' => [],
        ];
    }
    if (!empty($row['username'])) {
        $teams[$teamId]['members'][] = $row['username'];
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teamleden overzicht - Escape Room</title>
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
      <h2>Teams en leden</h2>
      <div class="team-grid">
      <?php if (empty($teams)): ?>
        <p>Er zijn nog geen teams aangemaakt.</p>
      <?php else: ?>
        <?php foreach ($teams as $team): ?>
          <div class="team-card">
            <h3><?= htmlspecialchars($team['name']) ?></h3>
            <?php if (empty($team['members'])): ?>
              <p>Nog geen leden.</p>
            <?php else: ?>
              <ul>
                <?php foreach ($team['members'] as $member): ?>
                  <li><?= htmlspecialchars($member) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>
