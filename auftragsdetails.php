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
        $auftragsNr = (int)$_GET['AuftragsNr'];
        //echo $auftragsNr;


        // Construct the query for the data that we want to see

        $query = 'SELECT fertigung.Stadt, sku.Name, auftrag.SKUNr, gehoert_zu.Quantitaet, servicepartner.Firmenname, lager.Lagerstandort, servicepartner.VIPKunde, lager.Lagerstandort
        FROM gehoert_zu
        LEFT JOIN bestellung ON gehoert_zu.BestellNr = bestellung.BestellNr
        LEFT JOIN auftrag ON gehoert_zu.AuftragsNr = auftrag.AuftragsNr
        LEFT JOIN sku ON auftrag.SKUNr = sku.SKUNr
        LEFT JOIN servicepartner ON bestellung.ServicepartnerNr = servicepartner.ServicepartnerNr
        LEFT JOIN lager ON bestellung.LagerNr = lager.LagerNr
        LEFT JOIN fertigung ON auftrag.FertigungsNr = fertigung.FertigungsNr
        WHERE gehoert_zu.AuftragsNr = ' . $auftragsNr;


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
            <button onclick="window.location.href='management.php'">Hallo Management!</button>
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

<h2>Auftragsdetails für Auftrag Nummer  <?php echo( $auftragsNr)  ?> </h2>

<main>
    <table>
        <thead>
            <tr>
                <th>Fertigungsstätte</th>
                <th>Artikelname</th>
                <th>Artikelnummer</th>
                <th>Quantität</th>
                <th>Servicepartner</th>
                <th>VIP-Kunde?</th>
                <th>Bestellung für Lager:</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result) {

                foreach ($result as $auftrag) {
                    echo '<tr>';
                    echo '<td>' . $auftrag->Stadt . '</td>';
                    echo '<td>' . $auftrag->Name . '</td>';
                    echo '<td>' . $auftrag->SKUNr . '</td>';
                    echo '<td>' . $auftrag->Quantitaet . '</td>';
                    echo '<td>' . $auftrag->Firmenname . '</td>';
                    echo '<td>' . $auftrag->VIPKunde . '</td>';
                    echo '<td>' . $auftrag->Lagerstandort . '</td>';
                    echo '</tr>';
                  
                }
            } else {
                echo '<tr><td colspan="7">Keine Bestellungen gefunden.</td></tr>';
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
