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

    //Datenbankabfrage
    if($loginRichtig){
        // Construct the query for the data that we want to see
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

    // Warenkorb in Bestellung umwandeln 
    if ($loginRichtig) {
        // Bei Klicken von Bestellknopf
        if (isset($_POST['bestellen'])) {
            $sql = 'INSERT INTO `airlimited`.`bestellung` ('. $userType .'Nr'.') VALUES ('. $userID .');';
            
            $input = $db->query($sql);
            $feedback = 'Bestellung für '. $userType .' '. $userID . ' wurde erzeugt.';
        }
    }





    //
//
//

    // Warenkorb in Bestellposten umwandeln 
    if ($loginRichtig) {
        // Bei Klicken von Bestellknopf
        if (isset($_POST['bestellen'])) {
            $sql = 'INSERT INTO `airlimited`.`bestellung` ('. $userType .'Nr'.') VALUES ('. $userID .');';
            
            $input = $db->query($sql);
        }
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
            <button onclick="window.location.href='fertigung.html'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.html'" class="management-btn">Management</button>
            <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='konto.html'">Mein Konto</button>
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
            <table>
                <thead>
                    <tr>
                        <th>Bild</th>
                        <th>Artikel</th>
                        <th>Stückpreis</th>
                        <th>Menge</th>
                        <th>Gesamtpreis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        if ($loginRichtig) {
                            $summe_gesamt = 0;
                            foreach ($result as $sku) {
                                $summe = $sku->Preis * $sku->Menge;
                                echo '
                                <tr>
                                    <td><img src="product0001.jpg" alt="'. $sku->Name .'"></td>
                                    <td>'. $sku->Name .' (Artikelnummer: '. $sku->SKUNr .')</td>
                                    <td>'. $sku->Preis .' €</td>
                                    <td>'. $sku->Menge .'</td>
                                    <td>'. $summe .' €</td>
                                </tr>
                            ';
                            $summe_gesamt = $summe_gesamt + $summe;
                            }
                        }                         
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;">Gesamt:</td>
                        <td><?php if(isset($summe_gesamt)){echo $summe_gesamt;}?> €</td>
                    </tr>
                </tfoot>
            </table>
            <div class="login-button">
                <form method="POST" action="#">
                    <button type="submit" name="bestellen">Hier Bestellen</button>
                </form>
            </div>
            <?php if(isset($feedback)){echo '<p class = "feedback">'. $feedback .'</p>';} ?>
        </div>
    </main>
    
	

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
