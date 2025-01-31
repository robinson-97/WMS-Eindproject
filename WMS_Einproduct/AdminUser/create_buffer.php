<?php
include 'db.php'; // Databaseverbinding

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location:admin_login.php'); // Verwijs naar de inlogpagina
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buffer_name = $_POST['buffer_name'];
    $description = $_POST['description'];

    try {
        $sql = "INSERT INTO buffer_locations (name, description) VALUES (:name, :description)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $buffer_name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            echo "Bufferlocatie succesvol aangemaakt: " . htmlspecialchars($buffer_name);
        } else {
            echo "Fout bij het aanmaken van de bufferlocatie.";
        }
    } catch (Exception $e) {
        echo "Er ging iets mis: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bufferlocatie Aanmaken</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Gebruik de globale main.css -->
    <link rel="stylesheet" href="../css/AdminUser/create_buffer.css"> <!-- Specifieke styling voor create_buffer -->
</head>
<body>
<!-- Navbar -->
<header class="navbar">
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="admin_home.php">Home</a>
        <a href="logout.php">Uitloggen</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Bufferlocatie Aanmaken</h1>

    <!-- Formulier voor het aanmaken van een bufferlocatie -->
    <form method="POST" action="create_buffer.php">
        <div class="form-container">
            <label for="buffer_name" class="form-label">Bufferlocatienaam:</label>
            <input type="text" id="buffer_name" name="buffer_name" class="form-input" required><br><br>

            <label for="description" class="form-label">Beschrijving:</label>
            <textarea id="description" name="description" class="form-input"></textarea><br><br>

            <button type="submit" class="btn">Aanmaken</button>
        </div>
    </form>
</main>
</body>
</html>
