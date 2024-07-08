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

// Suchfeld
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

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
$auftragsResult = $db->getEntityArray($query);

// Query for the service partners
$servicePartnerQuery = '
    SELECT ServicepartnerNr, Firmenname, `Nachname Kontaktperson`, `Vorname Kontaktperson`, Straße, HausNr, Stadt, PLZ, TelefonNr, `E-Mail`, VIPKunde
    FROM servicepartner
';
if ($searchTerm != '') {
    $servicePartnerQuery .= " WHERE Firmenname LIKE '%$searchTerm%'";
}
$servicePartnerResult = $db->getEntityArray($servicePartnerQuery);

// Update service partner data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = '';
    if (isset($_POST['update_service_partner'])) {
        $servicePartnerNr = $_POST['ServicepartnerNr'];
        $nachname = $_POST['NachnameKontaktperson'];
        $vorname = $_POST['VornameKontaktperson'];
        $straße = $_POST['Straße'];
        $hausNr = $_POST['HausNr'];
        $stadt = $_POST['Stadt'];
        $plz = $_POST['PLZ'];
        $telefonNr = $_POST['TelefonNr'];
        $email = $_POST['Email'];
        $vipKunde = $_POST['VIPKunde'];

        $updateQuery = "
            UPDATE servicepartner
            SET `Nachname Kontaktperson` = '$nachname', `Vorname Kontaktperson` = '$vorname', 
                Straße = '$straße', HausNr = '$hausNr', Stadt = '$stadt', PLZ = '$plz', TelefonNr = '$telefonNr', 
                `E-Mail` = '$email', VIPKunde = '$vipKunde'
            WHERE ServicepartnerNr = $servicePartnerNr
        ";
        $db->query($updateQuery);
    } elseif (isset($_POST['delete_service_partner'])) {
        $servicePartnerNr = $_POST['ServicepartnerNr'];

        // Löschen der referenzierten Datensätze
        $deleteBestellungQuery = "DELETE FROM bestellung WHERE ServicepartnerNr = $servicePartnerNr";
        $db->query($deleteBestellungQuery);

        $deleteQuery = "DELETE FROM servicepartner WHERE ServicepartnerNr = $servicePartnerNr";
        $db->query($deleteQuery);

        // Redirect nach dem Löschen
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['add_service_partner'])) {
        $firmenname = $_POST['Firmenname'];
        $nachname = $_POST['NachnameKontaktperson'];
        $vorname = $_POST['VornameKontaktperson'];
        $straße = $_POST['Straße'];
        $hausNr = $_POST['HausNr'];
        $stadt = $_POST['Stadt'];
        $plz = $_POST['PLZ'];
        $telefonNr = $_POST['TelefonNr'];
        $email = $_POST['Email'];
        $vipKunde = $_POST['VIPKunde'];

        // Serverseitige Validierung
        if (empty($firmenname) || empty($nachname) || empty($vorname) || empty($straße) || empty($hausNr) || 
            empty($stadt) || empty($plz) || empty($telefonNr) || empty($email) || empty($vipKunde)) {
            $error = 'Bitte füllen Sie alle Felder aus.';
        } else {
            $addQuery = "
                INSERT INTO servicepartner (Firmenname, `Nachname Kontaktperson`, `Vorname Kontaktperson`, Straße, HausNr, Stadt, PLZ, TelefonNr, `E-Mail`, VIPKunde)
                VALUES ('$firmenname', '$nachname', '$vorname', '$straße', '$hausNr', '$stadt', '$plz', '$telefonNr', '$email', '$vipKunde')
            ";
            $db->query($addQuery);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}
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
            <button onclick="window.location.href='onlineshop.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.php'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
            <button onclick="window.location.href='index.php'" class="login-btn">Anmelden</button>
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

<main>
    <?php 
        if($loginRichtig) {
            echo '<h2 class="content-header">Auftragsübersicht für das Management</h2>';
            echo '<div class="product-content">';
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
                if (isset($auftragsResult)) {
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
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                    ';
                    foreach ($auftragsResult as $auftrag) {
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
                                <td><a href="auftragsdetails.php?AuftragsNr=' . $auftrag->AuftragsNr . '">Details</a></td>
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
            echo '</div>';

            echo '<h2 class="content-header">Neuen Kunden hinzufügen</h2>';
            echo '
                <form method="POST" action="">
                    <table>
                        <tr>
                            <td><input type="text" name="Firmenname" placeholder="Firmenname" required></td>
                            <td><input type="text" name="NachnameKontaktperson" placeholder="Nachname Kontaktperson" required></td>
                            <td><input type="text" name="VornameKontaktperson" placeholder="Vorname Kontaktperson" required></td>
                            <td><input type="text" name="Straße" placeholder="Straße" required></td>
                            <td><input type="text" name="HausNr" placeholder="HausNr" required></td>
                            <td><input type="text" name="Stadt" placeholder="Stadt" required></td>
                            <td><input type="text" name="PLZ" placeholder="PLZ" required></td>
                            <td><input type="text" name="TelefonNr" placeholder="TelefonNr" required></td>
                            <td><input type="email" name="Email" placeholder="E-Mail" required></td>
                            <td>
                                <label for="VIPKunde">VIP-Kunde</label>
                                <select name="VIPKunde" required>
                                    <option value="">Bitte wählen</option>
                                    <option value="Ja">Ja</option>
                                    <option value="Nein">Nein</option>
                                </select>
                            </td>
                            <td><button type="submit" name="add_service_partner">Hinzufügen</button></td>
                        </tr>
                    </table>
                </form>
            ';

            echo '<h2 class="content-header">Kundenübersicht</h2>';
            echo '
                <form method="GET" action="">
                    <label for="search">Suche nach Firmenname:</label>
                    <input type="text" id="search" name="search" value="' . htmlspecialchars($searchTerm) . '">
                    <button type="submit">Suchen</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>ServicepartnerNr</th>
                            <th>Firmenname</th>
                            <th>Nachname Kontaktperson</th>
                            <th>Vorname Kontaktperson</th>
                            <th>Straße</th>
                            <th>HausNr</th>
                            <th>Stadt</th>
                            <th>PLZ</th>
                            <th>TelefonNr</th>
                            <th>Email</th>
                            <th>VIP-Kunde</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
            ';
                if ($servicePartnerResult) {
                    foreach ($servicePartnerResult as $partner) {
                        echo '
                            <tr>
                                <form method="POST" action="">
                                    <td>' . $partner->ServicepartnerNr . '<input type="hidden" name="ServicepartnerNr" value="' . $partner->ServicepartnerNr . '"></td>
                                    <td>' . $partner->Firmenname . '</td>
                                    <td><input type="text" name="NachnameKontaktperson" value="' . $partner->{"Nachname Kontaktperson"} . '"></td>
                                    <td><input type="text" name="VornameKontaktperson" value="' . $partner->{"Vorname Kontaktperson"} . '"></td>
                                    <td><input type="text" name="Straße" value="' . $partner->Straße . '"></td>
                                    <td><input type="text" name="HausNr" value="' . $partner->HausNr . '"></td>
                                    <td><input type="text" name="Stadt" value="' . $partner->Stadt . '"></td>
                                    <td><input type="text" name="PLZ" value="' . $partner->PLZ . '"></td>
                                    <td><input type="text" name="TelefonNr" value="' . $partner->TelefonNr . '"></td>
                                    <td><input type="email" name="Email" value="' . $partner->{"E-Mail"} . '"></td>
                                    <td>
                                        <label for="VIPKunde_' . $partner->ServicepartnerNr . '">VIP-Kunde</label>
                                        <select name="VIPKunde" id="VIPKunde_' . $partner->ServicepartnerNr . '">
                                            <option value="Ja" ' . ($partner->VIPKunde == "Ja" ? 'selected' : '') . '>Ja</option>
                                            <option value="Nein" ' . ($partner->VIPKunde == "Nein" ? 'selected' : '') . '>Nein</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="update_service_partner">Speichern</button>
                                        <button type="submit" name="delete_service_partner" onclick="return confirm(\'Sind Sie sicher, dass Sie diesen Servicepartner löschen möchten?\')">Löschen</button>
                                    </td>
                                </form>
                            </tr>
                        ';
                    }
                } else {
                    echo '<tr><td colspan="12">Keine Servicepartner gefunden.</td></tr>';
                }
                echo '
                    </tbody>
                </table>
            ';
        echo '</div>';
    } else {
        echo '<p class = "feedback">' . $feedback . '</p>';
    }
    ?>
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
