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

// Standortfilter
$standort = isset($_GET['standort']) ? $_GET['standort'] : 1;

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
    WHERE fertigung.FertigungsNr = '. $standort .'
    GROUP BY auftrag.AuftragsNr
    ORDER BY auftrag.Reihenfolge ASC;
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
            if($loginRichtig) {
                echo '
                    <form method="GET" action="">
                        <label for="standort">Fertigungsstandort:</label>
                        <select name="standort" id="standort" onchange="this.form.submit()">
                            <option value="1" ' . ($standort == 1 ? 'selected' : '') . '>Berlin</option>
                            <option value="2" ' . ($standort == 2 ? 'selected' : '') . '>München</option>
                            <option value="3" ' . ($standort == 3 ? 'selected' : '') . '>Rom</option>
                            <option value="4" ' . ($standort == 4 ? 'selected' : '') . '>Barcelona</option>
                            <option value="5" ' . ($standort == 5 ? 'selected' : '') . '>Mailand</option>
                            <option value="6" ' . ($standort == 6 ? 'selected' : '') . '>Lissabon</option>
                            <option value="7" ' . ($standort == 7 ? 'selected' : '') . '>Prag</option>
                            <option value="8" ' . ($standort == 8 ? 'selected' : '') . '>Warschau</option>
                            <option value="9" ' . ($standort == 9 ? 'selected' : '') . '>Budapest</option>
                            <option value="10" ' . ($standort == 10 ? 'selected' : '') . '>Kopenhagen</option>
                        </select>
                    </form>
                ';
                if (isset($result)) {
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
                                </tr>
                            </thead>
                            <tbody id="sortable">
                    ';
                    foreach ($result as $auftrag) {
                        echo '
                            <tr data-id="' . $auftrag->AuftragsNr . '">
                                <td>' . $auftrag->Reihenfolge . '</td>
                                <td>' . $auftrag->AuftragsNr . '</td>
                                <td>' . $auftrag->Fertigungsstandort . '</td>
                                <td>' . $auftrag->Name . '</td>
                                <td>' . $auftrag->SKUNr . '</td>
                                <td>' . $auftrag->Losgroesse . '</td>
                                <td>' . $auftrag->Bestand . '</td>
                                <td>' . $auftrag->Status . '</td>
                            </tr>
                        ';
                    }
                    echo'
                            </tbody>
                        </table>
                        <button id="saveOrder">Reihenfolge speichern</button>
                    ';
                }
                if(isset($feedback)){echo '<p class="feedback">'. $feedback .'</p>';} 
            }
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
</footer>

<script>
$(function() {
    $("#sortable").sortable();
    $("#sortable").disableSelection();

    $("#saveOrder").click(function() {
        var order = [];
        $("#sortable tr").each(function() {
            order.push($(this).data("id"));
        });

        $.post("update_order.php", { order: order }, function(response) {
            if (response.status === 'success') {
                alert("Reihenfolge erfolgreich gespeichert.");
                location.reload();
            } else {
                alert("Fehler beim Speichern der Reihenfolge.");
            }
        }, "json");
    });
});
</script>
</body>
</html>
