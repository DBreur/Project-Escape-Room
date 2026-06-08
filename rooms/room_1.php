<?php
session_start();
require_once('../dbcon.php');

$teamName = 'Geen team geselecteerd';
if (!empty($_SESSION['user_id'])) {
  $teamStmt = $db_connection->prepare('SELECT t.team FROM teams t JOIN team_members tm ON t.id = tm.team_id WHERE tm.user_id = ? LIMIT 1');
  $teamStmt->execute([$_SESSION['user_id']]);
  $teamRow = $teamStmt->fetch(PDO::FETCH_ASSOC);
  if ($teamRow) {
    $teamName = $teamRow['team'];
  } else {
    $teamName = 'Nog geen team';
  }
}

try {
  $stmt = $db_connection->query("SELECT * FROM questions WHERE roomId = 1");
  $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Databasefout: " . $e->getMessage());
}

$roomQuestions = [];
foreach ($riddles as $index => $riddle) {
  $numericCode = preg_replace('/[^0-9]/', '', $riddle['answer']);
  if ($numericCode === '') {
    $numericCode = (string) ($index + 1);
  }
  $roomQuestions[] = [
    'id' => $riddle['id'] ?? ($index + 1),
    'question' => $riddle['question'],
    'hint' => $riddle['hint'],
    'answer' => $riddle['answer'],
    'code' => $numericCode,
  ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escape Room 1</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="room1">
  <h1>Team: <?= htmlspecialchars($teamName) ?></h1>

  <h1 class="time"></h1>
  <object data="../img/hotspots_kelder.svg" type="image/svg+xml" class="svg-overlay"></object>

  <section class="question-list">
    <h2>Room 1 vragen</h2>
    <?php foreach ($roomQuestions as $index => $question) : ?>
      <article class="question-card">
        <h3>Vraag <?php echo $index + 1; ?></h3>
        <p><strong>Vraag:</strong> <?php echo htmlspecialchars($question['question']); ?></p>
        <p><strong>Hint:</strong> <?php echo htmlspecialchars($question['hint']); ?></p>
        <p><strong>Antwoord:</strong> <?php echo htmlspecialchars($question['answer']); ?></p>
        <button type="button" onclick="startQuestion(<?php echo $index; ?>)">Bekijk vraag in modal</button>
      </article>
    <?php endforeach; ?>
  </section>

  <section class="overlay" id="overlay" onclick="closeModal()"></section>

  <section class="modal" id="modal">
    <h2>Escape Room Vraag</h2>
    <p id="riddle"></p>
    <input type="text" id="answer" placeholder="Typ je antwoord">
    <button onclick="checkAnswer()">Verzenden</button>
    <p id="feedback"></p>
  </section>

  <section id="escapeModal" class="modal-overlay">
    <section class="modal-content">
        <span class="close-btn" onclick="closeEscapeModal()">&times;</span>
        <section id="modal-body-content">
        </section>
    </section>
  </section>

  <script>
    window.room1Questions = <?php echo json_encode($roomQuestions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <script src="../js/app.js"></script>

</body>

</html>