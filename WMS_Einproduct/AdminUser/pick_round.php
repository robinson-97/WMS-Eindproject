<?php
session_start();

// Controleer of de gebruiker is ingelogd als admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Redirect to admin login page
    exit(); // Stop script execution after redirection
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $round_name = $_POST['round_name'];

    try {
        // Maak de picking ronde aan
        $sql = "INSERT INTO picking_rounds (round_name, start_date) VALUES (:round_name, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':round_name', $round_name);
        $stmt->execute();

        echo "<p class='success-message'>Picking ronde succesvol aangemaakt!</p>";
    } catch (PDOException $e) {
        echo "<p class='error-message'>Fout bij het aanmaken van de picking ronde: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maak Picking Ronde</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Verbind de algemene main.css -->
    <link rel="stylesheet" href="../css/AdminUser/pick_round.css"> <!-- Verbind de pagina-specifieke CSS -->
</head>

<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="admin_home.php">Home</a>
        </nav>
    </div>
</header>

<main class="container">
    <h1 class="page-title">Maak een Nieuwe Picking Ronde</h1>
    <form method="POST" action="pick_round.php" class="form-container">
        <div class="form-group">
            <label for="round_name">Naam van de Ronde:</label>
            <input type="text" id="round_name" name="round_name" class="form-control" placeholder="Vul de naam van de picking ronde in" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-submit">Maak Picking Ronde</button>
        </div>
    </form>
</main>

<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>
</body>
</html>
