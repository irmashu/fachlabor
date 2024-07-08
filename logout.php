<?php
session_start();

// Alle Session-Variablen löschen
$_SESSION = array();

// Falls die Session ein Cookie verwendet, lösche das Cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Die Session zerstören
session_destroy();

// Weiterleitung zur Login-Seite oder einer anderen Seite
header('Location: index.php');
exit();
?>
