<?php
session_start();
// Überprüfen, ob die Variablen in der Session gesetzt sind
if (isset($_SESSION['userType']) && isset($_SESSION['userID'])) {
    $userType = $_SESSION['userType'];
    $userID = $_SESSION['userID'];

    $userTypeText = "Angemeldet als: " . $userType . " ";
    $userIDText = $userID . "<br>";
} else {
    $userTypeText = "Nicht angemeldet". "<br>";
    $userIDText = '';
}

// richtigen Login Prüfen
$loginRichtig = FALSE;
if (isset($userID) and isset($userType)) {
    if($userType == 'fertigung'){
        $loginRichtig = TRUE;
    }
}
if (!$loginRichtig) {
    $feedback = 'Bitte als Fertigung anmelden';
}

// Get Access to our database
require_once "db_class.php";

$DBServer   = 'localhost';
$DBHost     = 'airlimited';
$DBUser     = 'root';
$DBPassword = '';

$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
$db->connect();

if ($loginRichtig) {
    // Construct the query for the data that we want to see
    $query = 'SELECT auftrag.Reihenfolge, auftrag.AuftragsNr, sku.Name, auftrag.SKUNr, SUM(gehoert_zu.Quantitaet) AS Losgröße, sku.Fertigungsanweisungen, auftrag.Status';
    $query .= ' FROM auftrag';
    $query .= ' LEFT JOIN sku ON auftrag.SKUNr = sku.SKUNr';
    $query .= ' LEFT JOIN gehoert_zu ON auftrag.AuftragsNr = gehoert_zu.AuftragsNr';
    $query .= ' WHERE auftrag.FertigungsNr = '. $userID .' ';
    $query .= ' GROUP BY auftrag.AuftragsNr';
    $query .= ' ORDER BY auftrag.AuftragsNr DESC;';

    // Query the data
    $result = $db->getEntityArray($query);
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirLimited - Fertigung</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="AirLimited Logo">
        </div>
        <h1>Willkommen im AirLimited Shop</h1>
        <nav>
            <button onclick="window.location.href='onlineshop.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.php'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
            <button onclick="window.location.href='index.php'" class="login-btn">Anmelden</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='fertigung.php'">Hallo Fertigung!</button>
        </div>
        <div class="meine-logindaten">
            <p>
                <?php
                echo $userTypeText;
                echo $userIDText;
                ?>
            </p>
        </div>
    </header>

    <main>
    <?php
        if ($loginRichtig && isset($result)) {
            echo '
                <h2 class="content-header">Auftragsübersicht</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Reihenfolge</th>
                            <th>Auftragsnummer</th>
                            <th>Artikelname</th>
                            <th>Artikelnummer</th>
                            <th>Losgröße</th>
                            <th>Fertigungsanweisungen</th>
                            <th>Auftragsstatus</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody> 
                ';

            if ($result) {
                foreach ($result as $auftrag) {
                    echo '<tr>';
                    echo '<td>' . $auftrag->Reihenfolge . '</td>';
                    echo '<td>' . $auftrag->AuftragsNr . '</td>';
                    echo '<td>' . $auftrag->Name . '</td>';
                    echo '<td>' . $auftrag->SKUNr . '</td>';
                    echo '<td>' . $auftrag->Losgröße . '</td>';
                    echo '<td>' . $auftrag->Fertigungsanweisungen . '</td>';
                    echo '<td>';
                    echo '<form method="post" action="update_status.php">';
                    echo '<input type="hidden" name="AuftragsNr" value="' . $auftrag->AuftragsNr . '">';
                    echo '<select name="Status">';
                    echo '<option value="In Auftrag"' . ($auftrag->Status == 'In Auftrag' ? ' selected' : '') . '>In Auftrag</option>';
                    echo '<option value="In Bearbeitung"' . ($auftrag->Status == 'In Bearbeitung' ? ' selected' : '') . '>In Bearbeitung</option>';
                    echo '<option value="Fertig"' . ($auftrag->Status == 'Fertig' ? ' selected' : '') . '>Fertig</option>';
                    echo '</select>';
                    echo '<input type="submit" value="Ändern">';
                    echo '</form>';
                    echo '</td>';
                    echo '<td><a href="fertigungsdetails.php?AuftragsNr=' . urlencode($auftrag->AuftragsNr) . '">Details anzeigen</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="8">Kein Auftrag gefunden.</td></tr>';
            }
            echo '
                    </tbody>
                </table>
            ';
        }
        if (isset($feedback)) {
            echo '<p class="feedback">' . $feedback . '</p>';
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>

    <?php
    // Erfolgreiche Benachrichtigung anzeigen
    if (isset($_SESSION['success_message'])) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                alert("' . $_SESSION['success_message'] . '");
            });
        </script>';
        unset($_SESSION['success_message']);
    }
    ?>
</body>
</html>
