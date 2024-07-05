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

// richtigen Login Prüfen
$loginRichtig = FALSE;
if (isset($userID) and isset($userType)) {
    if($userType == 'servicepartner' or $userType == 'lager'){
        $loginRichtig = TRUE;
    }
}

// Beim Drücken der Knöpfe
if ($loginRichtig) {
    if (isset($_POST['bearbeiten'])) {
        $skuNr = $_POST['skuNr'];
        $Menge = $_POST['menge'];

        // Menge aktualisieren
        $sql = 'UPDATE `airlimited`.`warenkorb` SET `menge` = '. $Menge .' WHERE `SKUNr` = '. $skuNr .' AND `'. $userType .'Nr` = '. $userID .';';
        $input = $db->query($sql);

        // Seite Neu laden (damit die aktualisierten Daten angezeigt werden)
        header("Refresh:0");
        exit();
    }

    if (isset($_POST['löschen'])) {
        $skuNr = $_POST['skuNr'];

        // Artikel löschen
        $sql = 'DELETE FROM `airlimited`.`warenkorb` WHERE `SKUNr` = '. $skuNr .' AND `'. $userType .'Nr` = '. $userID .';';
        $input = $db->query($sql);

        // Seite Neu laden (damit die aktualisierten Daten angezeigt werden)
        header("Refresh:0");
        exit();
    }

    // Bei Klicken von Bestellknopf
    if (isset($_POST['bestellen'])) {
        // Bestellung erzeugen
        $datum = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO `airlimited`.`bestellung` (`Bestelldatum`, '. $userType .'Nr'.') VALUES ("'. $datum .'", '. $userID .');';
        $input = $db->query($sql);

        // Erzeugte BestellNr speichern
        if ($input) {
            $erzeugte_BestellNr = $db->getAutoIncID();
        }
        
        // Warenkorb in Bestellposten umwandeln
        $query = 'SELECT * FROM `airlimited`.`warenkorb` ';
        $query .= 'LEFT JOIN `airlimited`.`sku` ON warenkorb.SKUNr = sku.SKUNr ';
        $query .= 'WHERE warenkorb.'. $userType .'Nr = '. $userID . ' ';
        $query .= 'ORDER BY warenkorb.SKUNr ';
        $query .= 'LIMIT 1000;';

        // Query the data
        $result = $db->getEntityArray($query);
        
        foreach ($result as $sku) {
            $sql = 'INSERT INTO `airlimited`.`bestellposten` (`BestellNr`, `Quantität`, `SKUNr`) VALUES ('. $erzeugte_BestellNr .', '. $sku->Menge .', '. $sku->SKUNr .');';
            $input = $db->query($sql);
        }

        $feedback = 'Bestellung für '. $userType .' '. $userID . ' wurde erzeugt.';
    }

    // Bei Klicken von Leeren
    if (isset($_POST['leeren'])) {
        $sql = 'DELETE FROM `airlimited`.`warenkorb` WHERE warenkorb.'. $userType .'Nr = '. $userID . ' ;';
        $input = $db->query($sql);

        // Seite Neu laden (Ohne alten Warenkorb)
        header("Refresh:0");
        exit();
    }

    //Datenbankabfrage
    $query = 'SELECT * FROM `airlimited`.`warenkorb` ';
    $query .= 'LEFT JOIN `airlimited`.`sku` ON warenkorb.SKUNr = sku.SKUNr ';
    $query .= 'WHERE warenkorb.'. $userType .'Nr = '. $userID . ' ';
    $query .= 'ORDER BY warenkorb.SKUNr ';
    $query .= 'LIMIT 1000;';

    // Query the data
    $result = $db->getEntityArray($query);
} else {
    $feedback = 'Bitte als Servicepartner oder Lager anmelden';
}
?>



<!DOCTYPE html>
<html lang="de">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirLimited - Warenkorb</title>
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
    <h2>Warenkorb</h2>
    <main>
        <div class="product-content">
            <?php 
                if ($loginRichtig) {
                    echo '
                        <table>
                            <thead>
                                <tr>
                                    <th>Bild</th>
                                    <th>Artikel</th>
                                    <th>Stückpreis</th>
                                    <th>Menge</th>
                                    <th>Gesamtpreis</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                        '; 
                            
                        $summe_gesamt = 0;
                        foreach ($result as $sku) {
                            $summe = $sku->Preis * $sku->Menge;
                            echo '
                                <tr>
                                    <td> <img src="images/' . htmlspecialchars($sku->SKUNr) . '.jpg" alt="Produkt ' . htmlspecialchars($sku->SKUNr) . '" width="150" height="150"> </td>
                                    <td>'. $sku->Name .' (Artikelnummer: '. $sku->SKUNr .')</td>
                                    <td>'. $sku->Preis .' €</td>
                                    <td>
                                        <form method="POST" action="#">
                                            <input type="hidden" name="skuNr" value="'. $sku->SKUNr .'">
                                            <input type="number" name="menge" value="'. $sku->Menge .'" min="1">
                                            <button type="submit" name="bearbeiten">Aktualisieren</button>
                                            <button type="submit" name="löschen" class="red">Löschen</button>
                                        </form>
                                    </td>
                                    <td>'. $summe .' €</td>
                                </tr>
                            ';
                        $summe_gesamt = $summe_gesamt + $summe;
                        }
                        echo '
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right;">Gesamt:</td>
                                    <td>'. $summe_gesamt .' €</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="login-button">
                            <form method="POST" action="#">
                                <button type="submit" name="bestellen">Hier Bestellen</button>
                                <button type="submit" name="leeren" class="red">Warenkorb leeren</button>
                            </form>
                        </div>
                    ';
                }                         
            ?>
            
            <?php if(isset($feedback)){echo '<p class="feedback">'. $feedback .'</p>';} ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
