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

        // Versandt erst auf 0 setzen
       $updateAllQuery = "UPDATE gehoert_zu SET Versandt = 0 WHERE BestellNr IN (" . implode(',', array_map('intval', $bestellNrList)) . ")";
       $db->query($updateAllQuery);

        // Versandt auf 1 setzen
        if (!empty($versandtList)) {
            $updateSelectedQuery = "UPDATE gehoert_zu SET Versandt = 1 WHERE BestellNr IN (" . implode(',', array_map('intval', $versandtList)) . ")";
            $db->query($updateSelectedQuery);
        }

        header("Location: fertigungsdetails.php?AuftragsNr=" . $_GET['AuftragsNr']);
        exit;
    } else {
        echo "Ungültige Anfrage.";
    }

    $db->disconnect();
} else {
    echo "Unzureichende Berechtigungen.";
}
?>
