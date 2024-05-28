<?php
session_start();
// Überprüfen, ob die Variablen in der Session gesetzt sind
if (isset($_SESSION['userType']) && isset($_SESSION['userID'])) {
    $userType = $_SESSION['userType'];
    $userID = $_SESSION['userID'];

    $userTypeText = "Angemeldet als: " . $userType . " ";
    $userIDText = $userID . "<br>";
} else {
    $userTypeText = "Nicht Angemeldet". "<br>";
    $userIDText = '';
}

    // Get Access to our database
    require_once "db_class.php";

    $DBServer   = 'localhost';
	$DBHost     = 'airlimited';
	$DBUser     = 'root';
	$DBPassword = '';
	
	$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
	$db->connect();

    // Construct the query for the data that we want to see
    $query = 'SELECT auftrag.Reihenfolge, auftrag.AuftragsNr, sku.Name, auftrag.SKUNr, SUM(gehoert_zu.Quantitaet) AS Losgröße, sku.Fertigungsanweisungen, auftrag.Status';
    $query .= ' FROM auftrag';
    $query .= ' LEFT JOIN sku ON auftrag.SKUNr = sku.SKUNr';
    $query .= ' LEFT JOIN gehoert_zu ON auftrag.AuftragsNr = gehoert_zu.AuftragsNr';
    $query .= ' GROUP BY auftrag.AuftragsNr';


    // Query the data
   $result = $db->getEntityArray($query);

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
            <img src="logo.png" alt="AirLimited Logo"> <!-- Hier dein Logo einfügen -->
        </div>
        <h1>Willkommen im AirLimited Shop</h1>
        <nav>
            <button onclick="window.location.href='index.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.php'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
            <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
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

<h2>Auftragsübersicht</h2>

<main>
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
            <?php
            if ($result) {

                foreach ($result as $auftrag) {
                    echo '<tr>';
                    echo '<td>' . $auftrag->Reihenfolge . '</td>';
                    echo '<td>' . $auftrag->AuftragsNr. '</td>';
                    echo '<td>' . $auftrag->Name.'</td>';
                    echo '<td>' . $auftrag->SKUNr . '</td>';
                    echo '<td>' . $auftrag->Losgröße . '</td>';
                    echo '<td>' . $auftrag->Fertigungsanweisungen . '</td>';
                    echo '<td>' . $auftrag->Status . '</td>';
                    echo '<td><a href="fertigungsdetails.php?AuftragsNr=' . urlencode($auftrag->AuftragsNr) . '">Details anzeigen</a></td>';
                    echo '</tr>';
                  
                }
            } else {
                echo '<tr><td colspan="5">Keine Bestellungen gefunden.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</main>

<footer>
    <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
</footer>
</body>
</html>


