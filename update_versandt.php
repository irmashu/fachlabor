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
        $auftragsNrList = $_POST['AuftragsNr'];
        $versandtList = isset($_POST['Versandt']) ? $_POST['Versandt'] : [];
        //echo print_r($_POST, true);
        
        for ($i = 0; $i < count($bestellNrList); $i++) {
            $bestellNr = $bestellNrList[$i];
            $versandtStatus = $_POST['Versandt'][$i];
            //echo print_r($versandtStatus, true);
            $versandtValue = 0;
            if ($versandtStatus == true) {
                $versandtValue = 1;
            }
            $updateAllQuery = "UPDATE gehoert_zu SET Versandt = $versandtValue WHERE BestellNr = $bestellNr AND AuftragsNr = $auftragsNrList";
            $db->query($updateAllQuery);

        }

        header("Location: fertigungsdetails.php?AuftragsNr=" . $_POST['AuftragsNr']);
        exit;
    } 
    
    else {
        echo "Ungültige Anfrage.";
    }

    $db->disconnect();
} else {
    echo "Unzureichende Berechtigungen.";
}
?>
