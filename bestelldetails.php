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

//Bestellnr aus der URL getten
if (isset($_GET['BestellNr'])) {
    $bestellnr = htmlspecialchars($_GET['BestellNr']);
} else {
    $bestellnr = 1;
}

// Construct the query for the data that we want to see
$query = 'SELECT DISTINCT bestellposten.BestellpostenNr, sku.`Name`, bestellposten.SKUNr, sku.Preis, bestellposten.Quantität, auftrag.`Status`';
$query .= ' FROM bestellposten';
$query .= ' LEFT JOIN sku ON bestellposten.SKUNr = sku.SKUNr';
$query .= ' LEFT JOIN bestellung ON bestellposten.BestellNr = bestellung.BestellNr';
$query .= ' LEFT JOIN gehoert_zu ON bestellung.BestellNr = gehoert_zu.BestellNr';
$query .= ' LEFT JOIN auftrag ON gehoert_zu.AuftragsNr = auftrag.AuftragsNr';
$query .= ' WHERE bestellposten.BestellNr = ' . $bestellnr;

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
        <button onclick="window.location.href='onlineshop.php'">Onlineshop</button>
        <button onclick="window.location.href='fertigung.php'" class="fertigung-btn">Fertigung</button>
        <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
        <button onclick="window.location.href='index.php'" class="login-btn">Anmelden</button>
    </nav>
    <?php
        if(isset($userType)){
            if($userType == "servicepartner" OR $userType == "lager"){
                echo '
                <div class="account-buttons">
                <button onclick="window.location.href=`konto.php`">Mein Konto</button>
                <button onclick="window.location.href=`warenkorb.php`">Warenkorb</button>
                </div>';
            }
        }
    ?>
    <div class="meine-logindaten">
        <p>
            <?php
                echo $userTypeText;
                echo $userIDText;
            ?>
        </p>
    </div>
</header>
<h2> Hallo Kunde! - Meine Bestelldetails </h2>
<!-- <button onclick="window.location.href='account.html'" style="margin-left:40px;">Meine Accountdetails ändern</button> */ -->
<main>
    <table>
        <thead>
            <tr>
                <th>Bestellposten</th>
                <th>Artikelname</th>
                <th>Artikelnummer</th>
                <th>Kosten</th>
                <th>Quantität</th>
                <th>Auftragsstatus</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result) {
                foreach ($result as $bestellposten) {
                    echo '<tr>';
                    echo '<td>' . $bestellposten->BestellpostenNr . '</td>';
                    echo '<td>' . $bestellposten->Name. '</td>';
                    echo '<td>' . $bestellposten->SKUNr. '</td>';
                    echo '<td>' . $bestellposten->Preis . '</td>';
                    echo '<td>' . $bestellposten->Quantität . '</td>';
                    echo '<td>' . $bestellposten->Status . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6">Keine Bestellungen gefunden.</td></tr>';
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
