<?php
session_start();
require_once "db_class.php";

$DBServer   = 'localhost';
$DBHost     = 'airlimited';
$DBUser     = 'root';
$DBPassword = '';

$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
$db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order = $_POST['order'];
    
    foreach ($order as $position => $auftragsNr) {
        $position = intval($position) + 1;
        $auftragsNr = intval($auftragsNr);
        $updateQuery = "UPDATE auftrag SET Reihenfolge = $position WHERE AuftragsNr = $auftragsNr";
        $db->query($updateQuery);
    }

    echo json_encode(['status' => 'success']);
}
?>
