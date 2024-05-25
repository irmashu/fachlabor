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
$query = 'SELECT bestellung.Bestelldatum, bestellung.BestellNr, SUM(sku.Preis) AS Bestellsumme, auftrag.Status';
$query .= ' FROM bestellung';
$query .= ' LEFT JOIN gehört_zu ON bestellung.BestellNr = gehört_zu.BestellNr';
$query .= ' LEFT JOIN auftrag ON gehört_zu.AuftragsNr = auftrag.AuftragsNr';
$query .= ' LEFT JOIN bestellposten ON bestellung.BestellNr = bestellposten.BestellNr';
$query .= ' LEFT JOIN sku ON bestellposten.SKUNr = sku.SKUNr';
$query .= ' WHERE bestellung.ServicepartnerNr = '. $userID; 
$query .= ' GROUP BY bestellung.BestellNr';
$query .= ' LIMIT 1000';

// Query the data
$result = $db->getEntityArray($query);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirLimited - Mein Konto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="logo.png" alt="AirLimited Logo"> <!-- Hier dein Logo einfügen -->
    </div>
    <h1>Willkommen im AirLimited Shop!</h1>
    <nav>
        <button onclick="window.location.href='index.php'">Onlineshop</button>
        <button onclick="window.location.href='fertigung.html'" class="fertigung-btn">Fertigung</button>
        <button onclick="window.location.href='management.html'" class="management-btn">Management</button>
        <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
    </nav>
    <div class="account-buttons">
        <button onclick="window.location.href='konto.php'">Mein Konto</button>
        <button onclick="window.location.href='warenkorb.html'">Warenkorb</button>
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
<h2> Hallo Kunde! - Meine Bestellungen </h2>
<button onclick="window.location.href='account.html'" style="margin-left:40px;">Meine Accountdetails ändern</button>
<main>
    <table>
        <thead>
            <tr>
                <th>Bestelldatum</th>
                <th>Bestellnummer</th>
                <th>Bestellsumme</th>
                <th>Auftragsstatus</th>
                <th>Bestelldetails</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result) {
                foreach ($result as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['Bestelldatum']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['BestellNr']) . '</td>';
                    echo '<td>' . number_format($row['Bestellsumme'], 2, ',', '.') . ' €</td>';
                    echo '<td>' . htmlspecialchars($row['Status']) . '</td>';
                    echo '<td><a href="bestelldetails.php?BestellNr=' . urlencode($row['BestellNr']) . '">Details anzeigen</a></td>';
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
