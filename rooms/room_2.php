<?php
require_once('../dbcon.php');

try {
  $stmt = $db_connection->query("SELECT * FROM questions WHERE roomId = 2");
  $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Databasefout: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>De gang</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="room2">
  <h1 class="time"></h1>
  <object data="../img/hotspots_gang.svg" type="image/svg+xml" class="svg-overlay"></object>

  <!-- <div class="container">
    <?php foreach ($riddles as $index => $riddle) : ?>
    <div class="box box<?php echo $index + 1; ?>" onclick="openModal(<?php echo $index; ?>)"
      data-index="<?php echo $index; ?>" data-riddle="<?php echo htmlspecialchars($riddle['question']); ?>"
      data-answer="<?php echo htmlspecialchars($riddle['answer']); ?>">
      Box <?php echo $index + 1; ?>
    </div>
    <?php endforeach; ?>
  </div> -->

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

  <script src="../js/app.js"></script>

</body>

</html>