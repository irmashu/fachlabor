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

        $updateQuery = "UPDATE auftrag SET Status = ? WHERE AuftragsNr = ?";
        $stmt = mysqli_prepare($db->getConnection(), $updateQuery);
        mysqli_stmt_bind_param($stmt, 'ss', $status, $auftragsNr);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: fertigung.php");
            exit;
        } else {
            echo "Fehler beim Aktualisieren des Status: " . mysqli_error($db->getConnection());
        }
    } else {
        echo "Ungültige Anfrage.";
    }

    $db->disconnect();
} else {
    echo "Unzureichende Berechtigungen.";
}
?>
