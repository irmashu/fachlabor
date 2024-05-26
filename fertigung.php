<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirLimited - Fertigung</title>
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
            <button onclick="window.location.href='management.php'" class="management-btn">Management</button>
            <button onclick="window.location.href='login.php'" class="login-btn">Anmelden</button>
        </nav>
        <div class="account-buttons">
            <button onclick="window.location.href='fertigung.html'">Hallo Fertigung!</button>
        </div>
    </header>

<h2>Auftragsübersicht</h2>

<main>

    <table>
        <thead>
            <tr>
                <th>Reihenfolge</th>
                <th>Auftragsnummer</th>
                <th>Artikel</th>
                <th>SKUNr.</th>
                <th>Losgröße</th>
                <th>Fertigungsanweisungen</th>
                <th>Auftragsstatus</th>
                <th>Lieferdetails</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>0001</td>
                <td>Lüfterblatt</td>
                <td>0001</td>
                <td>20</td>
                <td>0001.pdf</td>
                <td>
                    <select>
                        <option value="In Auftrag">In Auftrag</option>
                        <option value="In Fertigung">In Fertigung</option>
                        <option value="Fertig">Fertig</option>
                    </select>
                </td>
                <td><a href="lieferdetails.html">Lieferdetails anzeigen</a></td>
            </tr>
        </tbody>
    </table>
</main>

<footer>
    <p>&copy; 2024 AirLimited. Alle Rechte vorbehalten.</p>
</footer>
</body>
</html>

