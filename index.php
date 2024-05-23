<?php
    require_once "db_class.php";

    // Get Access to our database
    $DBServer   = 'localhost';
	$DBHost     = 'airlimited';
	$DBUser     = 'root';
	$DBPassword = '';
	
	$db = new DBConnector($DBServer, $DBHost, $DBUser, $DBPassword);
	$db->connect();
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirLimited</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
   <header>
        <div class="logo">
            <img src="logo.png" alt="AirLimited Logo"> <!-- Hier dein Logo einfügen -->
        </div>
        <h1>Willkommen im AirLimited Shop</h1>
        <nav>
            <button onclick="window.location.href='index.php'">Onlineshop</button>
            <button onclick="window.location.href='fertigung.html'" class="fertigung-btn">Fertigung</button>
            <button onclick="window.location.href='management.html'" class="management-btn">Management</button>
            <button onclick="window.location.href='login.html'" class="login-btn">Login</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='konto.html'">Mein Konto</button>
            <button onclick="window.location.href='warenkorb.html'">Warenkorb</button>
        </div>
    </header>


    <main>
        <div class="product-nav">
            <h2>Navigation</h2>
            <form action="#">
                <label for="price">Preis:</label>
                <input type="number" id="price" name="price_min" min="0">
                <span>bis</span>
                <input type="number" id="price" name="price_max" min="0">
                <label for="category">Kategorie:</label>
                <select id="category" name="category">
                    <option value="all">Alle</option>
                    <option value="category1">Kategorie 1</option>
                    <option value="category2">Kategorie 2</option>
                    <!-- Weitere Kategorien hier einfügen -->
                </select>
                <label for="sort">Sortieren nach:</label>
                <select id="sort" name="sort">
                    <option value="price_asc">Preis aufsteigend</option>
                    <option value="price_desc">Preis absteigend</option>
                    <option value="number_asc">Artikelnummer aufsteigend</option>
                    <option value="number_desc">Artikelnummer absteigend</option>
                </select>
                <button type="submit">Filtern</button>
            </form>
        </div>
        <div class="product-content">
            <div class="product">
                <a href="#">
                    <div class="product-image">
                        <img src="product0001.jpg" alt="Produkt 0001" width="150" height="150">
                    </div>
                    <div class="product-details">
                        <h3><a href="0001.html">Lüfterblatt</a></h3>
                        <p>Artikelnummer: 0001</p>
                        <p>Hochwertiges Lüfterblatt für Industrieanlagen</p>
                        <p class="price">Preis: 25,99€</p> <!-- Hier den Preis des Produkts einfügen -->
                        <p>Lieferstatus: Versandbereit</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>



