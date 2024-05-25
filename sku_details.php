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
    $query = 'SELECT `Name`, `SKUNr`, `Foto`, `Preis`, `Verfuegbarkeit` , `Laenge`,  `Breite`,  `Hoehe`,  `Gewicht`,  `Beschreibung` ';
    $query .= 'FROM `airlimited`.`sku` ';
    $query .= 'WHERE `SKUNr` = '. $sku . ' ';
    $query .= 'LIMIT 1000;';

    // Query the data
    $result = $db->getEntityArray($query);
    $skuDB = $result[0];

/*     echo $query;
    echo '<br>';
    echo json_encode($result[0]);
    echo '<br>';
    echo $result[0]->SKUNr; */


    // Warenkorb füllen
    // Login überprüfen
    if (isset($userType) and isset($userID)) {
        // userType überprüfen
        if ($userType == 'servicepartner' or $userType == 'lager' ) {
            // quantity überprüfen
            if (isset($_POST['quantity'])) {
                // Nach userType auswählen
                if ($userType == 'servicepartner') {
                    $sql = 'INSERT INTO `warenkorb` (`ServicepartnerNr`, `SKUNr`, `Menge`) ';
                    $sql .= 'VALUES ('. $userID .' , '. $sku .', '. $_POST['quantity'] .');';
                } elseif ($userType == 'lager') {
                    $sql = 'INSERT INTO `warenkorb` ( `LagerNr`, `SKUNr`, `Menge`) ';
                    $sql .= 'VALUES ('. $userID .' , '. $sku .', '. $_POST['quantity'] .');';
                }

                $input = $db->query($sql);
                $feedback = $skuDB->Name .' wurde '. $_POST['quantity'] .' mal in den Warenkorb gelegt.';
            } else {
                $feedback = 'Mindestens 1 Produkt wählen.';
            }
        } else {
            $feedback = 'Zum Hinzufügen bitte als Servicepartner oder Lager anmelden.';
        }
    } else {
        $feedback = 'Zum Hinzufügen bitte anmelden.';
    }
    
    



    
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

    <main>
        <div class="product-content">
            <div class="product">
                <div class="product-image">
                    <img src="product0001.jpg" alt="Produkt 0001" width="150" height="150">
                </div>
                <div class="product-details">
                    <?php
                        echo '
                            <h3>'. $skuDB->Name .'</h3>
                            <p>Artikelnummer: '. $skuDB->SKUNr .'</p>
                            <p>'. $skuDB->Beschreibung .'</p>
                            <p class="price">Preis: '. $skuDB->Preis .' €</p>
                            <p>Verfügbarkeit: '. $skuDB->Verfuegbarkeit .'</p>
                        ';
                    ?>
                    <form action="#" class="order-form" method="POST">
                        <label for="quantity">Menge:</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1">
                        <input type="hidden" name="sku" value="<?php echo htmlspecialchars($sku); ?>">
                        <button type="submit">In den Warenkorb</button>
                        
                            <?php 
                                //Feedback sobald Produkt hinzugefügt wurde
                                echo $feedback;
                            ?>
                        
                    </form>
                </div>
            </div>
            <div class="product-details-container">
                <?php
                    echo '
                        <h2>Technisches Datenblatt</h2>
                        <table>
                            <tr>
                                <td>Länge [mm]:</td>
                                <td>'. $skuDB->Laenge .'</td>
                            </tr>
                            <tr>
                                <td>Breite [mm]:</td>
                                <td>'. $skuDB->Breite .'</td>
                            </tr>
                            <tr>
                                <td>Höhe [mm]:</td>
                                <td>'. $skuDB->Hoehe .'</td>
                            </tr>
                            <tr>
                                <td>Gewicht [kg]:</td>
                                <td>'. $skuDB->Gewicht .'</td>
                            </tr>
                        </table>
                    ';
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>


