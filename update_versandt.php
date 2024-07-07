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
        $bestellNrList = $_POST['BestellNr'];
        $auftragsNr = $_POST['AuftragsNr'];
        $versandtList = isset($_POST['Versandt']) ? $_POST['Versandt'] : [];
        $allReady = true;

        for ($i = 0; $i < count($bestellNrList); $i++) {
            $bestellNr = $bestellNrList[$i];
            $versandtStatus = $versandtList[$i];

            if ($versandtStatus == 'Ja') {
                // Check if all order items are ready for shipping
                $checkQuery = "SELECT COUNT(*) as count FROM bestellposten WHERE BestellNr = $bestellNr AND versandbereit != 1";
                $checkResult = $db->getEntityArray($checkQuery);
                if ($checkResult[0]->count > 0) {
                    $allReady = false;
                    break;
                }
            }
        }

        if ($allReady) {
            for ($i = 0; $i < count($bestellNrList); $i++) {
                $bestellNr = $bestellNrList[$i];
                $versandtStatus = $versandtList[$i];
                $versandtValue = ($versandtStatus == 'Ja') ? 'Ja' : 'Nein';

                $updateQuery = "UPDATE gehoert_zu SET Versandt = '$versandtValue' WHERE BestellNr = $bestellNr AND AuftragsNr = $auftragsNr";
                $db->query($updateQuery);
            }

            header("Location: fertigungsdetails.php?AuftragsNr=" . $auftragsNr);
            exit;
        } else {
            $_SESSION['feedback'] = "Nicht alle Bestellpositionen sind versandbereit. Die Änderungen wurden nicht durchgeführt.";
            header("Location: fertigungsdetails.php?AuftragsNr=" . $auftragsNr);
            exit;
        }
    } else {
        echo "Ungültige Anfrage.";
    }

    $db->disconnect();
} else {
    echo "Unzureichende Berechtigungen.";
}
?>
