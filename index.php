<?php
// Session starten/ fortsetzen
session_start();

// Get Access to our database
require_once "db_class.php";

$DBServer   = 'localhost';
$DBHost     = 'airlimited';
$DBUser     = 'root';
$DBPassword = '';

$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
$db->connect();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Benutzereingaben sicher abrufen und verarbeiten
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $loginAbkuerzung = strtolower(substr($username, 0, 3)); //erste 3 Zeichen aus Username
    $id = strtolower(substr($username, 3));     // Alle Zeichen nach der 3. Stelle

    // Fehler durch String vermeiden
    if (!is_numeric($id)) {
        $id = 0;
    }

    //Überprüfen, ob logindaten existieren
    $loginRichtig = FALSE;
    
    // Query erstellen
    switch ($loginAbkuerzung) {
        case 'ser':
            $loginType = 'servicepartner';
            $query = 'SELECT * FROM `airlimited`.`servicepartner` WHERE ServicepartnerNr = '. $id .' LIMIT 1000;';
            break;
        case 'lag':
            $loginType = 'lager';
            $query = 'SELECT * FROM `airlimited`.`lager` WHERE LagerNr = '. $id .' LIMIT 1000;';
            break;
        case 'fer':
            $loginType = 'fertigung';
            $query = 'SELECT * FROM `airlimited`.`fertigung` WHERE FertigungsNr = '. $id .' LIMIT 1000;';
            break;
        case 'man':
            $loginType = 'management';
            $loginRichtig = TRUE;
            break;
        default:
            $feedback = 'Ungültige Anmeldedaten. Beispiel: fer9';
            break;
    }

    // Einträge in DB suchen
    if (isset($query)) {
        $result = $db->getEntityArray($query);
        
        if(!empty($result)){
            $result0 = $result[0];

            //Login richtig wenn ein Eintrag gefunden wurde
            if ($result0->Passwort == $password) {
                $loginRichtig = TRUE;
                $feedback = ' Erfolgreich angemeldet als '. $loginType .' ' . $id;
            }
            else {
                $feedback = ' Falsches Passwort. Probiere: 0';
            }
        }
        
        else {
            $feedback = $loginType . ' ' . $id . ' konnte nicht gefunden werden';
        }
    }

    // bei richtigea login Daten in Session übernehmen
    if ($loginRichtig) {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['userType'] = $loginType;
        $_SESSION['userID'] = $id;
    }
}   


// Überprüfen, ob die Variablen in der Session gesetzt sind
if (isset($_SESSION['userType']) && isset($_SESSION['userID'])) {
    $userType = $_SESSION['userType'];
    $userID = $_SESSION['userID'];

    $loginText = "Angemeldet als: " . $userType . " " . $userID;
} else {
    $loginText = "Nicht angemeldet". "<br>";
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
        
        <form action="#" method="POST" class="login-form">
            <div>
                <label for="username">Benutzername:</label>
                <input type="text" id="username" name="username">
                &nbsp;&nbsp;
                <label for="password">Passwort:</label>
                <input type="password" id="password" name="password">
            </div>
            
            <div class = login-button>
                <button type="submit">Anmelden</button>
            </div>
        </form>
        
        <div class = login-button>
            <form action="logout.php" method="GET">
                <button type="submit" class = red>Abmelden</button>
            </form>
        </div>
        <div>
            <p>
                <?php
                    if (isset($feedback)) {
                        echo '<p class = "feedback">'. $feedback .'</p>';
                    }
                ?>
            </p>
        </div>

    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>



