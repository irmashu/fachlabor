<?php

    //SKUNr aus der URL getten
    if (isset($_GET['sku'])) {
        $sku = htmlspecialchars($_GET['sku']);
    } else {
        $sku = 1;
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
    $query = 'SELECT `Name`, `SKUNr`, `Foto`, `Preis`, `Verfuegbarkeit` , `Länge`,  `Breite`,  `Hoehe`,  `Gewicht`,  `Beschreibung` ';
    $query .= 'FROM `airlimited`.`sku` ';
    $query .= 'WHERE `SKUNr` = '. $sku . ' ';
    $query .= 'LIMIT 1000;';

    echo $query;
    echo '<br>';


    // Query the data
    $result = $db->getEntityArray($query);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirLimited</title>
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
            <button onclick="window.location.href='login.html'" class="login-btn">Login</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='konto.html'">Mein Konto</button>
            <button onclick="window.location.href='warenkorb.html'">Warenkorb</button>
        </div>
    </header>


<main>
    <div class="product-content">
        <div class="product">
            <div class="product-image">
                <img src="product0001.jpg" alt="Produkt 0001" width="150" height="150">
            </div>
            <div class="product-details">
                <h3>Lüfterblatt</h3>
                <p>Artikelnummer: 0001</p>
                <p>Hochwertiges Lüfterblatt für Industrieanlagen</p>

               
                
                <p class="price">Preis: 25,99€</p>
                <p>Lieferstatus: Versandbereit</p>
                <form action="#" class="order-form">
                    <label for="quantity">Menge:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                    <button type="submit">In den Warenkorb</button>
                </form>
            </div>
        </div>
        <div class="product-details-container">
            <h2>Technisches Datenblatt</h2>
            <table>
                <tr>
                    <td>Länge [mm]:</td>
                    <td>30</td>
                </tr>
                <tr>
                    <td>Breite [mm]:</td>
                    <td>30</td>
                </tr>
                <tr>
                    <td>Höhe [mm]:</td>
                    <td>5</td>
                </tr>
                <tr>
                    <td>Gewicht [kg]:</td>
                    <td>0.5</td>
                </tr>
            </table>
        </div>
    </div>
</main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>


