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

    // richtigen Login Prüfen
    $loginRichtig = FALSE;
    if (isset($userID) and isset($userType)) {
        if($userType == 'management'){
            $loginRichtig = TRUE;
        }
    }
    if (!$loginRichtig) {
        $feedback = 'Bitte als Management anmelden';
    }

// Aktualisierung der Reihenfolge
if(isset($_POST['update_order']) && isset($_POST['AuftragsNr']) && isset($_POST['new_order'])) {
    $auftragsNr = $_POST['AuftragsNr'];
    $newOrder = $_POST['new_order'];

    $updateQuery = "UPDATE auftrag SET Reihenfolge = $newOrder WHERE AuftragsNr = $auftragsNr";
    $result = $db->query($updateQuery);
    
    if($result) {
        $feedback = "Reihenfolge erfolgreich aktualisiert.";
    } else {
        $feedback = "Fehler beim Aktualisieren der Reihenfolge.";
    }
}




    // Construct the query for the data that we want to see
    $query = '
        SELECT auftrag.Reihenfolge, auftrag.AuftragsNr, fertigung.Stadt AS Fertigungsstandort, sku.`Name` ,auftrag.SKUNr, sind_in.Bestand, auftrag.`Status`, 
        SUM(gehoert_zu.`Quantitaet`) AS Losgroesse, 
        servicepartner.`VIPKunde`
        FROM auftrag
        LEFT JOIN sku
        ON auftrag.SKUNr = sku.SKUNr
        LEFT JOIN fertigung
        ON auftrag.FertigungsNr = fertigung.FertigungsNr
        LEFT JOIN sind_in
        ON auftrag.SKUNr = sind_in.SKUNr
        LEFT JOIN gehoert_zu
        ON auftrag.AuftragsNr = gehoert_zu.AuftragsNr
        LEFT JOIN bestellung
        ON gehoert_zu.BestellNr = bestellung.BestellNr
        LEFT JOIN servicepartner
        ON bestellung.ServicepartnerNr = servicepartner.ServicepartnerNr
        GROUP BY auftrag.AuftragsNr;
    ';
 
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
            <button onclick="window.location.href='management.php'">Hallo Management!</button>
        </div>
        <div class="meine-logindaten">
            <p>
                <?php
                    echo $loginText;
                ?>
            </p>
        </div>
    </header>

<h2>Auftragsübersicht für das Management</h2>

<main>
    <div class="product-content">
        <?php 
            if($loginRichtig && isset($result)){
                echo '
                    <table>
                        <thead>
                            <tr>
                                <th>Reihenfolge</th>
                                <th>Auftragsnummer</th>
                                <th>Fertigungsstätte</th>
                                <th>Artikel</th>
                                <th>SKUNr.</th>
                                <th>Losgröße</th>
                                <th>Lagerbestand</th>
                                <th>Auftragsstatus</th>
                                <th>VIP</th>
                                <th>Auftragsdetails</th>
                            </tr>
                        </thead>
                        <tbody>
                ';
                foreach ($result as $auftrag) {
                    echo '
                                <tr>
                                    <form method="POST" action="">
                                    <td>
                                        <input type="hidden" name="AuftragsNr" value="' . $auftrag->AuftragsNr . '">
                                        <select name="new_order">
                                            <option value="1" ' . ($auftrag->Reihenfolge == 1 ? 'selected' : '') . '>1</option>
                                            <option value="2" ' . ($auftrag->Reihenfolge == 2 ? 'selected' : '') . '>2</option>
                                            <option value="3" ' . ($auftrag->Reihenfolge == 3 ? 'selected' : '') . '>3</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                        <button type="submit" name="update_order">Aktualisieren</button>
                                    </td>
                                    </form>
                                    <td>'. $auftrag->AuftragsNr .'</td>
                                    <td>'. $auftrag->Fertigungsstandort .'</td>
                                    <td>'. $auftrag->Name .'</td>
                                    <td>'. $auftrag->SKUNr .'</td>
                                    <td>'. $auftrag->Losgroesse .'</td>
                                    <td>'. $auftrag->Bestand .'</td>
                                    <td>'. $auftrag->Status .'</td>
                                    <td>'. $auftrag->VIPKunde .'</td>
                                    <td><a href="auftragsdetails.php?AuftragsNr='. $auftrag->AuftragsNr .'">Auftragsdetails anzeigen</a></td>
                                </tr>
                    ';
                }
                echo'
                        </tbody>
                    </table>
                ';
            }
            if(isset($feedback)){echo '<p class = "feedback">'. $feedback .'</p>';} 
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
</footer>
</body>
</html>




