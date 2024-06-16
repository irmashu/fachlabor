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

if (isset($_GET['AuftragsNr'])) {
    $AuftragsNr = (int)$_GET['AuftragsNr'];
}

// Construct the query for the data that we want to see
$query = 'SELECT sku.Name, auftrag.SKUNr, gehoert_zu.BestellNr, bestellung.ServicepartnerNr, servicepartner.Firmenname, 
    CONCAT(servicepartner.Straße, " " , servicepartner.HausNr, ", ", servicepartner.PLZ, " ", servicepartner.Stadt) AS ServicepartnerAdresse,
    bestellung.LagerNr, lager.Lagerstandort,
    CONCAT(lager.Straße, " " , lager.HausNr, ", ", lager.PLZ, " ", lager.Lagerstandort) AS LagerAdresse,
    gehoert_zu.Quantitaet, gehoert_zu.Versandt
    FROM gehoert_zu
    LEFT JOIN bestellung ON gehoert_zu.BestellNr = bestellung.BestellNr
    LEFT JOIN auftrag ON gehoert_zu.AuftragsNr = auftrag.AuftragsNr
    LEFT JOIN sku ON auftrag.SKUNr = sku.SKUNr
    LEFT JOIN servicepartner ON bestellung.ServicepartnerNr = servicepartner.ServicepartnerNr
    LEFT JOIN lager ON bestellung.LagerNr = lager.LagerNr
    WHERE gehoert_zu.AuftragsNr = ' . $AuftragsNr;

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

<h2>Auftrags- und Lieferdetails für Auftrag Nummer <?php echo $AuftragsNr ?></h2>

<main>
    <form method="post" action="update_versandt.php">
        <table>
            <thead>
                <tr>
                    <th>Artikelname</th>
                    <th>Artikelnummer</th>
                    <th>Bestellnummer</th>
                    <th>Kunde Servicepartner</th>
                    <th>Lieferadresse Servicepartner</th>
                    <th>Bestellung für Lager:</th>
                    <th>Lieferadresse Lager</th>
                    <th>Quantität</th>
                    <th>Versandt?</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result) {
                    foreach ($result as $bestellung) {
                        echo '<tr>';
                        echo '<td>' . $bestellung->Name . '</td>';
                        echo '<td>' . $bestellung->SKUNr . '</td>';
                        echo '<td>' . $bestellung->BestellNr . '</td>';
                        echo '<td>' . $bestellung->Firmenname . '</td>';
                        echo '<td>' . $bestellung->ServicepartnerAdresse . '</td>';
                        echo '<td>' . $bestellung->Lagerstandort . '</td>';
                        echo '<td>' . $bestellung->LagerAdresse . '</td>';
                        echo '<td>' . $bestellung->Quantitaet . '</td>';
                        echo '<td>';
                        echo '<input type="hidden" name="BestellNr[]" value="' . $bestellung->BestellNr . '">';
                        echo '<input type="hidden" name="AuftragsNr" value="' . $AuftragsNr . '">';
                        echo '<select name="Versandt[]">';
                        echo '<option value="1"' . ($bestellung->Versandt ? ' selected' : '') . '>Ja</option>';
                        echo '<option value="0"' . (!$bestellung->Versandt ? ' selected' : '') . '>Nein</option>';
                        echo '</select>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="9">Keine Bestellungen gefunden.</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <input type="submit" value="Änderungen speichern">
    </form>
</main>

<footer>
    <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
</footer>
</body>
</html>
