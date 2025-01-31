<?php
include 'db.php';  // Aangepast naar de juiste map voor db.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            $sql = "SELECT * FROM clientusers WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: order.php'); // Verwijs naar de bestelpagina, met correct pad
                exit;
            } else {
                echo "<p style='color:red;'>Ongeldige inloggegevens.</p>";
            }
        } catch (PDOException $e) {
            echo "Fout: " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "<p style='color:red;'>Alle velden zijn verplicht.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Client</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Correct pad naar de main.css -->
    <link rel="stylesheet" href="../css/ClientUser/client_login.css"> <!-- Correct pad naar de client_login.css -->
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>Client Login</h1>
        <nav>
            <a href="../index.html">Home</a> <!-- Terug naar de homepage in de rootmap -->
            <a href="../login.html">Terug</a> <!-- Terug naar de loginpagina in de rootmap -->
        </nav>
    </div>
</header>

<section class="login-section">
    <div class="container">
        <h1>Inloggen</h1>
        <form method="POST" action="client_login.php"> <!-- Dit blijft hetzelfde, formulier submit binnen dezelfde map -->
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Wachtwoord:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Inloggen</button>
        </form>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>
</body>
</html>
