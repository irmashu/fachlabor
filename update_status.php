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
                // Gesamtmenge der produzierten Artikel aus der Tabelle gehoert_zu berechnen
                $quantityQuery = "SELECT SUM(Quantitaet) as totalQuantity FROM gehoert_zu WHERE AuftragsNr = ?";
                $stmt = mysqli_prepare($db->getConnection(), $quantityQuery);
                mysqli_stmt_bind_param($stmt, 's', $auftragsNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $totalQuantity);
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

                // Berechnen der gesamten Kundenbestellmenge, die mit diesem Auftrag verknüpft ist
                $customerOrderQuantityQuery = "SELECT SUM(bp.Quantität) as customerQuantity
                                               FROM bestellposten bp
                                               JOIN gehoert_zu g ON bp.BestellNr = g.BestellNr
                                               JOIN bestellung b ON bp.BestellNr = b.BestellNr
                                               WHERE g.AuftragsNr = ? AND b.ServicepartnerNr IS NOT NULL";
                $stmt = mysqli_prepare($db->getConnection(), $customerOrderQuantityQuery);
                mysqli_stmt_bind_param($stmt, 's', $auftragsNr);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $customerQuantity);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Berechnen der Menge, die ins Lager geht
                $lagerQuantity = $totalQuantity - $customerQuantity;

                if ($lagerQuantity > 0) {
                    // Lagerbestand aktualisieren
                    $updateStockQuery = "UPDATE sind_in SET Bestand = Bestand + ? WHERE SKUNr = ?";
                    $stmt = mysqli_prepare($db->getConnection(), $updateStockQuery);
                    mysqli_stmt_bind_param($stmt, 'is', $lagerQuantity, $skuNr);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                header("Location: fertigung.php");
                exit;
            } else {
                echo "Fehler beim Aktualisieren des Status: " . mysqli_error($db->getConnection());
            }
        } else {
            $updateQuery = "UPDATE auftrag SET Status = ? WHERE AuftragsNr = ?";
            $stmt = mysqli_prepare($db->getConnection(), $updateQuery);
            mysqli_stmt_bind_param($stmt, 'ss', $status, $auftragsNr);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: fertigung.php");
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
