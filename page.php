<?php

//setup php for working with Unicode data
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');
require_once "db_class.php";
require_once "html_utils.php";

    if( $LOGGEDIN )
    { echo '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>';
    // Zu welcher Seite soll es gehen? (Wird hier aus den Parametern der gerufenen Datei index.php geholt)
    $page = 'findauthors';
    if ( isset ($_GET['page']) ) $page = GetHTMLParameter($_GET, 'page', null);
    if( $page != null )
    {
    $page = 'pages/'.strtolower($page).'.php';
    if( file_exists($page) )
    {
    require_once $page;
    }
    else
    echo '<p>Seite (' . $page . ') nicht gefunden! </p>';
    }
    echo '</html>';
    }

?>