<?php
    session_start();
    // Überprüfen, ob die Variablen in der Session gesetzt sind
    if (isset($_SESSION['userType']) && isset($_SESSION['userID'])) {
        $userType = $_SESSION['userType'];
        $userID = $_SESSION['userID'];

        $loginText = "Angemeldet als: " . $userType . " " . $userID;
    } else {
        $loginText = "Nicht angemeldet". "<br>";
    }

    // Get Access to our database
    require_once "db_class.php";

    $DBServer   = 'localhost';
	$DBHost     = 'airlimited';
	$DBUser     = 'root';
	$DBPassword = '';
	
	$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
	$db->connect();

    // Variablen für Filter getten 
    $price_min = 0;
    $price_max = 99999;

    if (isset($_GET['price_min']) and (int)$_GET['price_min'] <> 0 ) {
        $price_min = (int)$_GET['price_min'];
    }
    if (isset($_GET['price_max']) and (int)$_GET['price_max'] <> 0 ) {
        $price_max = (int)$_GET['price_max'];
    }
    if (!isset($_GET['sort'])) {
        $sort = 'SKUNr ASC';
    } else {
        $sort = $_GET['sort'];
    }

    
    // Construct the query for the data that we want to see
    $query = 'SELECT    `Foto`, `Name`, `SKUNr`, `Beschreibung`, `Preis`, `Verfuegbarkeit` ';
    $query .= 'FROM `airlimited`.`sku`';
    $query .= 'WHERE Preis > '. $price_min .' AND Preis < ' . $price_max . ' ';
    $query .= 'ORDER BY ' . $sort . ' ';
    $query .= 'LIMIT 1000;';

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
            <img src="logo.png" alt="AirLimited Logo">
        </div>
        <h1>Willkommen im AirLimited Shop!</h1>
        <nav>
            <button onclick="window.location.href='index.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.html'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
            <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='konto.html'">Mein Konto</button>
            <button onclick="window.location.href='warenkorb.php'">Warenkorb</button>
        </div>
        <div class="meine-logindaten">
            <p>
                <?php
                    echo $loginText;
                ?>
            </p>
        </div>
    </header>


    <main>
        <div class="product-nav">
            <h2>Navigation</h2>
            <form action="#" method="GET">
                <label for="price">Preis:</label>
                <input type="number" id="price" name="price_min" min="0">
                <span>bis</span>
                <input type="number" id="price" name="price_max" min="0">
                <label for="category">Kategorie:</label>
                <select id="category" name="category">
                    <option value="all">Alle</option>
                    <option value="category1">Kategorie 1</option>
                    <option value="category2">Kategorie 2</option>
                    <!-- Weitere Kategorien hier einfügen -->
                </select>
                <label for="sort">Sortieren nach:</label>
                <select id="sort" name="sort">
                    <option value="Preis ASC">Preis aufsteigend</option>
                    <option value="Preis DESC">Preis absteigend</option>
                    <option value="SKUNr ASC">Artikelnummer aufsteigend</option>
                    <option value="SKUNr DESC">Artikelnummer absteigend</option>
                </select>
                <button type="submit">Filtern</button>
            </form>
        </div>
        <div class="product-content">
            <?php
                //Eigentliche Liste erstellen
                foreach ( $result as $sku ){
                    echo '
                    <div class="product">
                        <a href="#">
                            <div class="product-image">
                                <img src="product0001.jpg" alt="Produkt 0001" width="150" height="150">
                            </div>
                            <div class="product-details">
                                <h3><a href="sku_details.php?sku=' . urlencode($sku->SKUNr) . '"> ' . htmlspecialchars($sku->Name) . ' </a></h3>
                                <p>Artikelnummer: '. $sku->SKUNr .'</p>
                                <p>'. $sku->Beschreibung .'</p>
                                <p class="price">Preis: '. $sku->Preis .'</p> <!-- Hier den Preis des Produkts einfügen -->
                                <p>Verfügbarkeit: '. $sku->Verfuegbarkeit .'</p>
                            </div>
                        </a>
                    </div>
                    ';
                }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>



