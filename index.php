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
  ?>

  <header class="site-header">
    <h1>Escape Room</h1>
    <div class="header-actions">
      <?php if (!empty($_SESSION['username'])): ?>
        <span>Welkom, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a class="header-button" href="logout.php">Uitloggen</a>
      <?php else: ?>
        <a class="header-button" href="login.php">Inloggen</a>
        <a class="header-button header-button-alt" href="register.php">Account maken</a>
      <?php endif; ?>
    </div>
  </header>
  <main>
  <h1>Welkom</h1>
  <p>Je bent een 12-jarige jongen en zit opgesloten in de kelder van je stiefmoeders huis.
Om te ontsnappen, moet je eerst de code van de kluis vinden om de reservesleutel er uit te halen waarmee je de kelderdeur kunt openen.
Als je eenmaal boven bent, kom je in de hal van het huis. Daar ligt ergens de sleutel van de voordeur verstopt.
Lukt het jou om alle hints te vinden en op tijd uit het huis te ontsnappen?</p>

  <p> Je hebt 15 minuten de tijd om te ontsnappen, dus wees snel en slim! </p>

  <button><a href="./rooms/room_1.php">Klik hier om de escape te starten </a></button>
</main>

<footer>
  <p>&copy; 2024 Escape Room. All rights reserved.</p>
</footer>
</body>

</html>