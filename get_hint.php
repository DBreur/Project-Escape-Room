<?php
// get_hint.php
require_once 'dbcon.php';

if (isset($_GET['object'])) {
    $object = $_GET['object'];

    switch ($object) {
        case 'voordeur':
            echo "<h2>De Rode Voordeur</h2>";
            echo "<p>De deur zit stevig op slot. Je hebt de zware voordeursleutel nodig om deze te openen.</p>";
            break;

        case 'pinbord':
            echo "<h2>Het Pinbord</h2>";
            // echo "<p>Er hangen verschillende briefjes op. Eén briefje valt op: er staat een vreemde datum op gekrabbeld...</p>";
            echo "<img src=''/>";
            break;

        case 'lamp':
            echo "<h2>De Tafellamp</h2>";
            echo "<p>Je kijkt onder de lampenkap. Je ziet niks, maar onder de voet van de lamp ligt een klein briefje!</p>";
            echo "<p></p>";
            break;

        case 'paneel':
            echo "<h2>Numeriek Paneel</h2>";
            echo "<p>Voer de 4-cijferige code in om de trapkast te ontgrendelen:</p>";
            echo "<form method='POST' action='room_2.php'>";
            echo "  <input type='number' name='kast_code' min='0000' max='9999' required>";
            echo "  <button type='submit'>Invoeren</button>";
            echo "</form>";
            break;

        default:
            echo "<h2>Onderzoek</h2>";
            echo "<p>Je ziet hier niks bijzonders.</p>";
            break;
    }
}
?>