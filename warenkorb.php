<?php
session_start();

// Überprüfen, ob die Variablen in der Session gesetzt sind
$userType = $_SESSION['userType'] ?? null;
$userID = $_SESSION['userID'] ?? null;
$loginText = isset($userType, $userID) ? "Angemeldet als: $userType $userID<br>" : "Nicht Angemeldet<br>";

// Zugriff auf die Datenbank erhalten
require_once "db_class.php";
$db = new DBConnector('localhost', 'airlimited', 'root', '');
$db->connect();

// Richtigen Login prüfen
$loginRichtig = isset($userID, $userType) && in_array($userType, ['servicepartner', 'lager']);

function updateStockAndProduction($db, $skuNr, $orderQty, $bestellNr) {
    $skuQuery = "
        SELECT s.Bestand, k.Standardlosgroeße, k.FertigungsNr, k.lagerNr
        FROM sind_in s 
        JOIN sku k ON s.SKUNr = k.SKUNr 
        WHERE s.SKUNr = '$skuNr'
    ";
    $skuResult = $db->getEntityArray($skuQuery);
    if (!$skuResult) return false;

    $currentStock = $skuResult[0]->Bestand;
    $batchSize = $skuResult[0]->Standardlosgroeße;
    $newStock = $currentStock - $orderQty;
    $fertigungsNr = $skuResult[0]->FertigungsNr;
    $lagerNr = $skuResult[0]->lagerNr;
    $aktuellesDatumZeit = date('Y-m-d H:i:s');

    // Fall A: Lagerbestand - Bestellmenge ist größer als die Losgröße, kein neuer Auftrag nötig
    if ($newStock >= $batchSize) {
        $db->query("UPDATE sind_in SET Bestand = Bestand - $orderQty WHERE SKUNr = '$skuNr'");
        return true;
    }

    // Fall C: Lagerbestand - Bestellmenge ist kleiner als 0, neuer Kundenauftrag und Lagerbestellung nötig
    if ($newStock < 0) {

          // Überprüfen, ob es bereits einen offenen Produktionsauftrag für diese SKU gibt, der groß genug ist
          $existingOrderQuery = "
          SELECT a.AuftragsNr, SUM(g.Quantitaet) AS Gesamtmenge
          FROM auftrag a
          JOIN gehoert_zu g ON a.AuftragsNr = g.AuftragsNr
          WHERE a.SKUNr = '$skuNr' AND a.Status = 'In Bearbeitung'
          GROUP BY a.AuftragsNr
          HAVING Gesamtmenge >= $batchSize
        ";
         $existingOrderResult = $db->getEntityArray($existingOrderQuery);

         if ($existingOrderResult) {
            // Es gibt einen bestehenden Auftrag, der groß genug ist
            // Aktualisieren Sie nur den Lagerbestand
            $db->query("UPDATE sind_in SET Bestand = Bestand - $orderQty WHERE SKUNr = '$skuNr'");
         } else {
            // Kundenbestellung entspricht der Differenz
            $customerOrderQty = abs($newStock);

            // Erstellen des neuen Produktionsauftrags
            $db->query("INSERT INTO auftrag (Auftragsdatum, Status, SKUNr, FertigungsNr) VALUES ('$aktuellesDatumZeit', 'In Bearbeitung', '$skuNr', '$fertigungsNr')");
            $productionOrderId = $db->getAutoIncID();

            // Verknüpfen des Produktionsauftrags mit der Kundenbestellung
            $db->query("INSERT INTO gehoert_zu (AuftragsNr, BestellNr, Quantitaet) VALUES ($productionOrderId, $bestellNr, $customerOrderQty)");

            // Erstellen einer neuen Lagerbestellung
            $db->query("INSERT INTO bestellung (Bestelldatum, lagerNr) VALUES ('$aktuellesDatumZeit', '$lagerNr')");
            $lagerBestellNr = $db->getAutoIncID();

            // Verknüpfen des Produktionsauftrags mit der Lagerbestellung
            $db->query("INSERT INTO gehoert_zu (AuftragsNr, BestellNr, Quantitaet) VALUES ($productionOrderId, $lagerBestellNr, $batchSize)");

            // Hinzufügen des Bestellpostens zur Lagerbestellung
            $db->query("INSERT INTO bestellposten (BestellNr, Quantität, SKUNr) VALUES ($lagerBestellNr, $batchSize, '$skuNr')");
         }
      
        return false;
    }

    // Fall B: Lagerbestand - Bestellmenge ist größer als 0, aber kleiner als die Losgröße, neuer Lagerauftrag nötig
    if ($newStock > 0 && $newStock < $batchSize) {
        // Überprüfen, ob es bereits einen offenen Produktionsauftrag für diese SKU gibt, der groß genug ist
        $existingOrderQuery = "
            SELECT a.AuftragsNr, SUM(g.Quantitaet) AS Gesamtmenge
            FROM auftrag a
            JOIN gehoert_zu g ON a.AuftragsNr = g.AuftragsNr
            WHERE a.SKUNr = '$skuNr' AND a.Status = 'In Bearbeitung'
            GROUP BY a.AuftragsNr
            HAVING Gesamtmenge >= $batchSize
        ";
        $existingOrderResult = $db->getEntityArray($existingOrderQuery);

        if ($existingOrderResult) {
            // Es gibt einen bestehenden Auftrag, der groß genug ist
            // Aktualisieren Sie nur den Lagerbestand
            $db->query("UPDATE sind_in SET Bestand = Bestand - $orderQty WHERE SKUNr = '$skuNr'");
        } else {
            // Es gibt keinen bestehenden Auftrag, der groß genug ist, daher einen neuen Lagerauftrag anlegen
            $db->query("INSERT INTO bestellung (Bestelldatum, lagerNr) VALUES ('$aktuellesDatumZeit', '$lagerNr')");
            $lagerBestellNr = $db->getAutoIncID();

            // Neuer Auftrag für die neue Bestellung
            $db->query("INSERT INTO auftrag (Auftragsdatum, Status, SKUNr, FertigungsNr) VALUES ('$aktuellesDatumZeit', 'In Bearbeitung', '$skuNr', '$fertigungsNr')");
            $newProductionOrderId = $db->getAutoIncID();

            // Neuen Produktionsauftrag für die Standardlosgröße hinzufügen
            $db->query("INSERT INTO gehoert_zu (AuftragsNr, BestellNr, Quantitaet) VALUES ($newProductionOrderId, $lagerBestellNr, $batchSize)");

            // Bestellposten für die neue Bestellung hinzufügen
            $db->query("INSERT INTO bestellposten (BestellNr, Quantität, SKUNr) VALUES ($lagerBestellNr, $batchSize, '$skuNr')");

            // Lagerbestand aktualisieren
            $db->query("UPDATE sind_in SET Bestand = Bestand - $orderQty WHERE SKUNr = '$skuNr'");
        }

        return true; // Rückgabe true in Fall B
    }

    return true; // Rückgabe true für den allgemeinen Fall
}

if ($loginRichtig) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $skuNr = $_POST['skuNr'] ?? null;
        $menge = $_POST['menge'] ?? null;

        if (isset($_POST['bearbeiten']) && $skuNr && $menge) {
            $db->query("UPDATE `airlimited`.`warenkorb` SET `Menge` = $menge WHERE `SKUNr` = $skuNr AND `{$userType}Nr` = $userID");
            header("Refresh:0");
            exit();
        }

        if (isset($_POST['löschen']) && $skuNr) {
            $db->query("DELETE FROM `airlimited`.`warenkorb` WHERE `SKUNr` = $skuNr AND `{$userType}Nr` = $userID");
            header("Refresh:0");
            exit();
        }

        if (isset($_POST['bestellen'])) {
            $datum = date('Y-m-d H:i:s');
            $db->query("INSERT INTO `airlimited`.`bestellung` (`Bestelldatum`, {$userType}Nr) VALUES ('$datum', $userID)");
            $erzeugte_BestellNr = $db->getAutoIncID();

            $query = "SELECT * FROM `airlimited`.`warenkorb` 
                      LEFT JOIN `airlimited`.`sku` ON warenkorb.SKUNr = sku.SKUNr 
                      WHERE warenkorb.{$userType}Nr = $userID 
                      ORDER BY warenkorb.SKUNr 
                      LIMIT 1000";
            $result = $db->getEntityArray($query);

            foreach ($result as $sku) {
                $skuNr = $sku->SKUNr;
                $menge = $sku->Menge;
                $db->query("INSERT INTO `airlimited`.`bestellposten` (`BestellNr`, `Quantität`, `SKUNr`) VALUES ($erzeugte_BestellNr, $menge, $skuNr)");
                $isReadyForShipment = updateStockAndProduction($db, $skuNr, $menge, $erzeugte_BestellNr);
                if ($isReadyForShipment) {
                    $db->query("UPDATE bestellposten SET versandbereit = 1 WHERE BestellNr = $erzeugte_BestellNr AND SKUNr = $skuNr");
                }
            }

            // Warenkorb leeren
            $db->query("DELETE FROM `airlimited`.`warenkorb` WHERE {$userType}Nr = $userID");

            $feedback = "Vielen Dank für Ihre Bestellung! Sie erhalten die Rechnung innerhalb der nächsten Stunde innerhalb Ihres hinterlegten E-Mail-Postfachs.";
        }

        if (isset($_POST['leeren'])) {
            $db->query("DELETE FROM `airlimited`.`warenkorb` WHERE {$userType}Nr = $userID");
            header("Refresh:0");
            exit();
        }
    }

    $query = "SELECT * FROM `airlimited`.`warenkorb` 
              LEFT JOIN `airlimited`.`sku` ON warenkorb.SKUNr = sku.SKUNr 
              WHERE warenkorb.{$userType}Nr = $userID 
              ORDER BY warenkorb.SKUNr 
              LIMIT 1000";
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
            <p><?php echo $loginText; ?></p>
        </div>
    </header>
    <h2>Warenkorb</h2>
    <main>
        <div class="product-content">
            <?php if ($loginRichtig) { ?>
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
                        $summe_gesamt = 0;
                        foreach ($result as $sku) {
                            $summe = $sku->Preis * $sku->Menge;
                        ?>
                            <tr>
                                <td><img src="images/<?php echo htmlspecialchars($sku->SKUNr); ?>.jpg" alt="Produkt <?php echo htmlspecialchars($sku->SKUNr); ?>" width="150" height="150"></td>
                                <td><?php echo $sku->Name; ?> (Artikelnummer: <?php echo $sku->SKUNr; ?>)</td>
                                <td><?php echo $sku->Preis; ?> €</td>
                                <td>
                                    <form method="POST" action="#">
                                        <input type="hidden" name="skuNr" value="<?php echo $sku->SKUNr; ?>">
                                        <input type="number" name="menge" value="<?php echo $sku->Menge; ?>" min="1">
                                        <button type="submit" name="bearbeiten">Aktualisieren</button>
                                        <button type="submit" name="löschen" class="red">Löschen</button>
                                    </form>
                                </td>
                                <td><?php echo $summe; ?> €</td>
                            </tr>
                        <?php $summe_gesamt += $summe; } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;">Gesamt:</td>
                            <td><?php echo $summe_gesamt; ?> €</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="login-button">
                    <form method="POST" action="#">
                        <button type="submit" name="bestellen">Hier Bestellen</button>
                        <button type="submit" name="leeren" class="red">Warenkorb leeren</button>
                    </form>
                </div>
            <?php } ?>
            <?php if (isset($feedback)) { echo '<p class="feedback">'. $feedback .'</p>'; } ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
