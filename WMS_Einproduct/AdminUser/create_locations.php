<?php
include 'db.php'; // Zorg voor databaseverbinding

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['location_name'])) {
    $locationName = trim($_POST['location_name']);

    if (!empty($locationName)) {
        try {
            $query = "INSERT INTO locations (name) VALUES (:location_name)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':location_name', $locationName);
            $stmt->execute();

            $message = "Locatie succesvol toegevoegd!";
        } catch (Exception $ex) {
            $message = "Er ging iets fout: " . $ex->getMessage();
        }
    } else {
        $message = "Locatie naam mag niet leeg zijn.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locatie Toevoegen</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Algemene stijlen -->
    <link rel="stylesheet" href="../css/AdminUser/create_locations.css"> <!-- Pagina-specifieke stijlen -->
</head>
<body>
<header class="navbar">
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="admin_home.php">Home</a>
        <a href="logout.php">Uitloggen</a>
    </nav>
</header>

<main class="main-container">
    <h1 class="page-title">Locatie Toevoegen</h1>
    <?php if (!empty($message)): ?>
        <div class="alert <?= strpos($message, 'succes') !== false ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="create_locations.php" class="form-container">
        <label for="location_name" class="form-label">Locatie Naam</label>
        <input type="text" name="location_name" id="location_name" class="form-input" placeholder="Voer locatie naam in" required>
        <button type="submit" class="btn">Toevoegen</button>
    </form>
</main>

</body>
</html>
