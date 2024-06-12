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

// richtigen Login Prüfen
$loginRichtig = FALSE;
if (isset($userID) and isset($userType)) {
    if($userType == 'servicepartner' or $userType == 'lager'){
        $loginRichtig = TRUE;
    }
}
if (!$loginRichtig) {
    $feedback = 'Bitte als Servicepartner oder Lager anmelden';
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
// $query = 'SELECT DISTINCT bestellung.Bestelldatum, bestellung.BestellNr, SUM(sku.Preis * bestellposten.Quantität) AS Bestellsumme, auftrag.Status';
$query = 'SELECT DISTINCT bestellung.Bestelldatum, bestellung.BestellNr, sku.Preis, bestellposten.Quantität, auftrag.Status';
$query .= ' FROM bestellung';
$query .= ' LEFT JOIN gehoert_zu ON bestellung.BestellNr = gehoert_zu.BestellNr';
$query .= ' LEFT JOIN auftrag ON gehoert_zu.AuftragsNr = auftrag.AuftragsNr';
$query .= ' LEFT JOIN bestellposten ON bestellung.BestellNr = bestellposten.BestellNr';
$query .= ' LEFT JOIN sku ON bestellposten.SKUNr = sku.SKUNr';
$query .= ' WHERE bestellung.'. $userType .'Nr = '. $userID; 
$query .= ' GROUP BY bestellung.BestellNr';
$query .= ' LIMIT 1000';


// Query the data
$result = $db->getEntityArray($query);
$bestellposten = array();
foreach($result as $bestellung){
    $postenquery = 'SELECT sku.SKUNr, sku.Preis, bestellposten.Quantität FROM bestellposten';
    $postenquery .= ' LEFT JOIN sku ON bestellposten.SKUNr = sku.SKUNr';
    $postenquery .= ' WHERE bestellposten.BestellNr = '. $bestellung->BestellNr;
    $postenresult = $db->getEntityArray($postenquery); //Ergebnis query 2 
    $bestellposten[$bestellung->BestellNr] = $postenresult;
}
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
        <button onclick="window.location.href='fertigung.php'" class="fertigung-btn">Fertigung</button>
        <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
        <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
    </nav>
    <div class="account-buttons">
        <button onclick="window.location.href='konto.php'">Mein Konto</button>
        <button onclick="window.location.href='warenkorb.php'">Warenkorb</button>
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
<!-- <button onclick="window.location.href='account.html'" style="margin-left:40px;">Meine Accountdetails ändern</button> */ -->
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
                // print("<pre>".print_r($bestellposten,true)."</pre>"); debug-ausgabe
                foreach ($result as $bestellung) {
                    echo '<tr>';
                    echo '<td>' . $bestellung->Bestelldatum . '</td>';
                    echo '<td>' . $bestellung->BestellNr. '</td>';
                    $sum = 0;
                    foreach($bestellposten[$bestellung->BestellNr] as $posten){
                        $sum += $posten->Preis * $posten->Quantität;
                    }
                    echo '<td>' . $sum.' €</td>';
                    echo '<td>' . $bestellung->Status . '</td>';
                    echo '<td><a href="bestelldetails.php?BestellNr=' . urlencode($bestellung->BestellNr) . '">Details anzeigen</a></td>';
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
