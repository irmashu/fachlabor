<?php

//setup php for working with Unicode data
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');
require_once "db_class.php";
require_once "html_utils.php";

// Session starten um Dialog zu ermöglichen
session_start(); // ab hier existiert $_SESSION - Zusammenhang schaffen
//! Name dieser PHP-Datei
$self = '//'. $_SERVER['SERVER_NAME'].'/index.php';

//Login checken ggf. abmelden
$LOGGEDIN = !empty($_SESSION['LoggedIn']);
$LoginShow = true;
if( isset($_GET['Logout'])){
    // User abmelden
    $_SESSION = array(); // Leeres Array  Session gelöscht
    session_destroy();
    $LOGGEDIN = false;
    $LoginShow = true;
}

// User Prüfen / anmelden
elseif( !empty($_GET['Username']) && !$LOGGEDIN ){
    // NEW LOGIN / start session
    //$username = $db->get_escape_string($_GET['Username']);
    $username = strtolower($_GET['Username']);
    $userKnown = true;
    if ($userKnown){
    // z.B. User-Daten anzeigen, Eintrag in ein Logfile, usw.
        $LOGGEDIN = true;
        $_SESSION['Username'] = $username;
        $_SESSION['LoggedIn'] = 1;
    }
    else // Authentifizierung ist fehlgeschlagen
    {
    // Z.B. Fehlermeldung einblenden oder anzeigen
    }
}

if( $LOGGEDIN)
{
echo '<p>Welcome ' . $_SESSION['Username'] . '! </p>';
require_once 'page.php';
}

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="AirLimited Logo">
        </div>
        <h1>Willkommen im AirLimited Shop!</h1>
        <nav>
            <button onclick="window.location.href='onlineshop.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.php'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
            <button onclick="window.location.href='index.php'" class="login-btn">Anmelden</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='konto.php'">Mein Konto</button>
            <button onclick="window.location.href='warenkorb.php'">Warenkorb</button>
        </div>
        <div class="meine-logindaten">
            <p>
                <?php
                    echo $loginText;
                ?>
            </p>
        </div>
    </header>

    <main class="main-login">
        
        <form method="get" action=" <?php echo $self.''.(JoinGet(array('Logout', 'Register'))); ?> " name="loginform" id="loginform" class="login-form">
            <div><h1>Einloggen</h1></div>
            <div><p>
                Username: &nbsp;&nbsp;&nbsp;<input type="text" name="Username" id="Username" maxlength=16/>
                Password: &nbsp;&nbsp;&nbsp;<input type="text" name="Password" id="Password" maxlength=16/>
            </p></div>
            <div class= login-button>
                <button type="submit" name="login" id="login" value="Login" >Anmelden</button>
            </div>
        </form>
        
        <div class = login-button>
            <form action="logout.php" method="GET">
                <button type="submit" class = red>Abmelden</button>
            </form>
        </div>

    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
