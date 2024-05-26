<?php

if (isset($_GET['auftragsNr'])) {
    $auftragsNr = (int)$_GET['auftragsNr'];
    echo $auftragsNr;
}

?>