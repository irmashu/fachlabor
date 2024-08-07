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

// Zugang zur Datenbank
require_once "db_class.php";

$DBServer   = 'localhost';
$DBHost     = 'airlimited';
$DBUser     = 'root';
$DBPassword = '';

$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
$db->connect();

// Variablen für Filter getten 
$price_min = 0;
$price_max = 1000;
$search_term = '';

if (isset($_GET['price_min']) and (int)$_GET['price_min'] <> 0 ) {
    $price_min = (int)$_GET['price_min'];
}
if (isset($_GET['price_max']) and (int)$_GET['price_max'] <> 0 ) {
    $price_max = (int)$_GET['price_max'];
}
if (isset($_GET['search_term'])) {
    $search_term = $_GET['search_term'];
}
if (!isset($_GET['sort'])) {
    $sort = 'SKUNr ASC';
} else {
    $sort = $_GET['sort'];
}

// Query erstellen
$query = 'SELECT `Foto`, `Name`,  sku.`SKUNr`, `Beschreibung`, `Preis`, sind_in.Bestand ';
$query .= 'FROM `airlimited`.`sku` ';
$query .= 'LEFT JOIN sind_in ON sku.SKUNr = sind_in.SKUNr ';
$query .= 'WHERE Preis > '. $price_min .' AND Preis < ' . $price_max . ' ';

if ($search_term != '') {
    $query .= 'AND (Name LIKE "%' . $search_term . '%" OR sku.SKUNr LIKE "%' . $search_term . '%") ';
}

$query .= 'ORDER BY ' . $sort . ' ';
$query .= 'LIMIT 1000;';

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
                    echo $loginText;
                ?>
            </p>
        </div>
    </header>

    <main id="index-page">
    <div class="product-nav">
        <h2>Navigation</h2>
        <form action="#" method="GET">
            <label for="search_term">Suche:</label>
            <input type="text" id="search_term" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Artikelnamen oder Artikelnummer">

            <label for="price">Preis:</label>
            <input type="number" id="price" name="price_min" min="0" value="<?php echo htmlspecialchars($price_min); ?>">
            <span>bis</span>
            <input type="number" id="price" name="price_max" min="0" value="<?php echo htmlspecialchars($price_max); ?>">

            <label for="sort">Sortieren nach:</label>
            <select id="sort" name="sort">
                <option value="Preis ASC" <?php if ($sort == 'Preis ASC') echo 'selected'; ?>>Preis aufsteigend</option>
                <option value="Preis DESC" <?php if ($sort == 'Preis DESC') echo 'selected'; ?>>Preis absteigend</option>
                <option value="SKUNr ASC" <?php if ($sort == 'SKUNr ASC') echo 'selected'; ?>>Artikelnummer aufsteigend</option>
                <option value="SKUNr DESC" <?php if ($sort == 'SKUNr DESC') echo 'selected'; ?>>Artikelnummer absteigend</option>
            </select>
            <button type="submit">Filtern</button>
        </form>
    </div>
    <div class="product-content">
        <?php
            foreach ( $result as $sku ){
                echo '
                <div class="product">
                    <a href="#">
                        <div class="product-image">
                            <img src="images/' . htmlspecialchars($sku->SKUNr) . '.jpg" alt="Produkt ' . htmlspecialchars($sku->SKUNr) . '" width="150" height="150">
                        </div>
                        <div class="product-details">
                            <h3><a href="sku_details.php?sku=' . urlencode($sku->SKUNr) . '"> ' . htmlspecialchars($sku->Name) . ' </a></h3>
                            <p>Artikelnummer: '. $sku->SKUNr .'</p>
                            <p>'. $sku->Beschreibung .'</p>
                            <p class="price">Preis: '. $sku->Preis .'</p>
                            <p>Noch '. $sku->Bestand .' Stück auf Lager</p>
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
