<?php
session_start();

if (isset($_SESSION['userType']) && $_SESSION['userType'] == 'fertigung') {
    require_once "db_class.php";

    $DBServer   = 'localhost';
    $DBHost     = 'airlimited';
    $DBUser     = 'root';
    $DBPassword = '';

    $db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
    $db->connect();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $auftragsNr = $_POST['AuftragsNr'];
        $status = $_POST['Status'];

        // Den aktuellen Status aus der Datenbank abrufen
        $currentStatusQuery = "SELECT Status FROM auftrag WHERE AuftragsNr = ?";
        $stmt = mysqli_prepare($db->getConnection(), $currentStatusQuery);
        mysqli_stmt_bind_param($stmt, 's', $auftragsNr);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $currentStatus);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Status aktualisieren und Enddatum setzen, wenn der Status auf "Fertig" geändert wird
        if ($status == 'Fertig' && $currentStatus != 'Fertig') {
            $endDatum = date('Y-m-d H:i:s');
            $updateQuery = "UPDATE auftrag SET Status = ?, Enddatum = ? WHERE AuftragsNr = ?";
            $stmt = mysqli_prepare($db->getConnection(), $updateQuery);
            mysqli_stmt_bind_param($stmt, 'sss', $status, $endDatum, $auftragsNr);

            if (mysqli_stmt_execute($stmt)) {
                // Gesamtproduktionsmenge aus der Tabelle gehoert_zu berechnen
                $quantityQuery = "SELECT SUM(Quantitaet) as totalProduction FROM gehoert_zu WHERE AuftragsNr = ?";
                $stmt = mysqli_prepare($db->getConnection(), $quantityQuery);
                mysqli_stmt_bind_param($stmt, 's', $auftragsNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $totalProduction);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Die SKUNr des Auftrags abrufen
                $skuQuery = "SELECT SKUNr FROM auftrag WHERE AuftragsNr = ?";
                $stmt = mysqli_prepare($db->getConnection(), $skuQuery);
                mysqli_stmt_bind_param($stmt, 's', $auftragsNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $skuNr);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Den aktuellen Lagerbestand abrufen
                $stockQuery = "SELECT Bestand FROM sind_in WHERE SKUNr = ?";
                $stmt = mysqli_prepare($db->getConnection(), $stockQuery);
                mysqli_stmt_bind_param($stmt, 's', $skuNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $currentStock);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Berechnen der Kundenbestellmenge aus der Tabelle bestellposten
                $customerOrderQuantityQuery = "SELECT SUM(bp.Quantität) as customerQuantity
                                               FROM bestellposten bp
                                               JOIN gehoert_zu g ON bp.BestellNr = g.BestellNr
                                               JOIN bestellung b ON bp.BestellNr = b.BestellNr
                                               WHERE g.AuftragsNr = ? AND bp.SKUNr = ? AND b.ServicepartnerNr IS NOT NULL";
                $stmt = mysqli_prepare($db->getConnection(), $customerOrderQuantityQuery);
                mysqli_stmt_bind_param($stmt, 'ss', $auftragsNr, $skuNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $customerQuantity);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Berechnung der Produktionsmenge für den Kunden und das Lager
                $batchSizeQuery = "SELECT Standardlosgroeße FROM sku WHERE SKUNr = ?";
                $stmt = mysqli_prepare($db->getConnection(), $batchSizeQuery);
                mysqli_stmt_bind_param($stmt, 's', $skuNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $batchSize);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Berechnung der zu produzierenden Menge
                $directToCustomer = max($customerQuantity - $currentStock, 0); // Was direkt an den Kunden geht
                $totalProduction = $directToCustomer + $batchSize; // Gesamtproduktionsmenge
                $lagerQuantity = $batchSize; // Menge für das Lager
                $newStock = $currentStock - $customerQuantity + $totalProduction; // Neuer Lagerbestand

                // Lagerbestand aktualisieren
                $updateStockQuery = "UPDATE sind_in SET Bestand = ? WHERE SKUNr = ?";
                $stmt = mysqli_prepare($db->getConnection(), $updateStockQuery);
                mysqli_stmt_bind_param($stmt, 'is', $newStock, $skuNr);
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Fehler beim Aktualisieren des Lagerbestands: " . mysqli_error($db->getConnection());
                    exit;
                }
                mysqli_stmt_close($stmt);

                // Status "versandbereit" für alle verknüpften Bestellungen auf 1 setzen
                $updateReadyQuery = "UPDATE bestellposten SET versandbereit = 1 WHERE BestellNr IN 
                                     (SELECT BestellNr FROM gehoert_zu WHERE AuftragsNr = ?)";
                $stmt = mysqli_prepare($db->getConnection(), $updateReadyQuery);
                mysqli_stmt_bind_param($stmt, 's', $auftragsNr);
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Fehler beim Aktualisieren des Versandbereit-Status: " . mysqli_error($db->getConnection());
                    exit;
                }
                mysqli_stmt_close($stmt);

                // Erfolgreiche Benachrichtigung in Session speichern
                $_SESSION['success_message'] = "Auftrag $auftragsNr wurde erfolgreich aktualisiert und Bestellposten auf versandbereit gesetzt.";
                header('Location: fertigung.php');
                exit;
            } else {
                echo "Fehler beim Aktualisieren des Status: " . mysqli_error($db->getConnection());
            }
        } else {
            $updateQuery = "UPDATE auftrag SET Status = ? WHERE AuftragsNr = ?";
            $stmt = mysqli_prepare($db->getConnection(), $updateQuery);
            mysqli_stmt_bind_param($stmt, 'ss', $status, $auftragsNr);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Auftrag $auftragsNr wurde erfolgreich aktualisiert.";
                header('Location: fertigung.php');
                exit;
            } else {
                echo "Fehler beim Aktualisieren des Status: " . mysqli_error($db->getConnection());
            }
        }
    } else {
        echo "Ungültige Anfrage.";
    }

    $db->disconnect();
} else {
    echo "Unzureichende Berechtigungen.";
}
?>
