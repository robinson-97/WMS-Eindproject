<?php
session_start();
include 'db.php'; // Verbind met je database

// Controleer of admin is ingelogd
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Redirect naar admin login als niet ingelogd
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Controleer of de gebruikersnaam al bestaat
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $error = "Deze gebruikersnaam bestaat al.";
    } else {
        // Voeg de nieuwe gebruiker toe als normaal account
        $hashedPassword = hash('sha256', $password); // Versleutel het wachtwoord
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        if ($stmt->execute()) {
            $success = "Gebruiker succesvol aangemaakt!";
        } else {
            $error = "Er is iets misgegaan bij het aanmaken van de gebruiker.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Gebruiker Aanmaken</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Algemene stijl -->
    <link rel="stylesheet" href="../css/AdminUser/create_users.css"> <!-- Pagina-specifieke stijl -->
</head>
<body>
<div class="navbar">
    <h1>Admin Dashboard</h1>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Uitloggen</a>
    </div>
</div>

<div class="container">
    <h1>Nieuwe Gebruiker Aanmaken</h1>
    <?php if (isset($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p class="success-message"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <form method="POST" class="form-create-user">
        <div class="form-group">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-create">Gebruiker Aanmaken</button>
    </form>
</div>

<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>
</body>
</html>
