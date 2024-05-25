<?php
// Session starten/ fortsetzen
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Benutzereingaben sicher abrufen und verarbeiten
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $loginType = htmlspecialchars($_POST['login-type']);
    $id = htmlspecialchars($_POST['id']);

    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    $_SESSION['userType'] = $loginType;
    $_SESSION['userID'] = $id;
}


// Überprüfen, ob die Variablen in der Session gesetzt sind
if (isset($_SESSION['userType']) && isset($_SESSION['userID'])) {
    $userType = $_SESSION['userType'];
    $userID = $_SESSION['userID'];

    $userTypeText = "Angemeldet als: " . $userType . " ";
    $userIDText = $userID . "<br>";
} else {
    $userTypeText = "Nicht Angemeldet". "<br>";
    $userIDText = '';
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
            <button onclick="window.location.href='index.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.html'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.html'" class="management-btn">Management</button>
            <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
        </nav>
        <div class="meine-logindaten">
            <p>
                <?php
                    echo $userTypeText;
                    echo $userIDText;
                ?>
            </p>
        </div>
    </header>

    <main>
        <form action="#" method="POST">
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username">
            
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password">

            <label for="login-type">Login als:</label>
            <select id="login-type" name="login-type">
                <option value="servicepartner">Servicepartner</option>
                <option value="lager">Lager</option>
                <option value="fertigung">Fertigung</option>
                <option value="management">Management</option>
            </select>
            
            <label for="id">Fertigungs-/ Lager/- Servicepartnernummer:</label>
            <input type="number" id="id" name="id" min=1 value=1>

            <button type="submit">Anmelden</button>
        </form>
        <form action="logout.php" method="GET">
            <button type="submit">Abmelden</button>
        </form>

    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>



