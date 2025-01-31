<?php
include 'db.php'; // Zorg dat je db.php juist is ingesteld voor de databaseverbinding

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Verwijs naar de inlogpagina
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verkrijg gegevens van het formulier
    $po_number = $_POST['po_number'];
    $po_quantity = intval($_POST['po_quantity']); // Verkrijg de hoeveelheid

    try {
        // Zoek naar beschikbare locaties (geen bufferlocaties in de database)
        $location_query = "SELECT * FROM locations";
        $location_stmt = $conn->prepare($location_query);
        $location_stmt->execute();
        $locations = $location_stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($locations) {
            // Selecteer willekeurig een locatie
            $selected_location = $locations[array_rand($locations)]['name'];

            // Voeg de nieuwe PO toe aan de database met de hoeveelheid
            $po_sql = "INSERT INTO purchase_orders (po_number, location, quantity, created_at, status) 
                       VALUES (:po_number, :location, :quantity, NOW(), 'closed')";
            $po_stmt = $conn->prepare($po_sql);
            $po_stmt->bindParam(':po_number', $po_number);
            $po_stmt->bindParam(':location', $selected_location);
            $po_stmt->bindParam(':quantity', $po_quantity);

            if ($po_stmt->execute()) {
                echo "<div class='success-message'>PO succesvol aangemaakt! Locatie toegewezen: " . htmlspecialchars($selected_location) . " | Hoeveelheid: " . htmlspecialchars($po_quantity) . "</div>";
            } else {
                echo "<div class='error-message'>Er is een fout opgetreden bij het aanmaken van de PO.</div>";
            }
        } else {
            echo "<div class='error-message'>Geen locaties beschikbaar in de database. Voeg locaties toe en probeer opnieuw.</div>";
        }
    } catch (Exception $ex) {
        echo "<div class='error-message'>Er ging iets fout bij het aanmaken van de PO: " . htmlspecialchars($ex->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe PO Aanmaken</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/AdminUser/create_po.css"> <!-- Externe stylesheet voor deze pagina -->
</head>
<body>
<div class="navbar">
    <h1>Admin Dashboard</h1>
    <div>
        <a href="admin_dashboard.php">Home</a>
        <a href="logout.php">Uitloggen</a>
    </div>
</div>

<h1>Nieuwe Purchase Order Aanmaken</h1>

<!-- Het formulier wordt hieronder weergegeven -->
<form method="POST" action="create_po.php">
    <div class="form-field">
        <label for="po_number">Naam product:</label>
        <input type="text" id="po_number" name="po_number" required>
    </div>

    <div class="form-field">
        <label for="po_quantity">Aantal producten:</label>
        <input type="number" id="po_quantity" name="po_quantity" min="1" required>
    </div>

    <button type="submit" class="submit-button">Aanmaken</button>
</form>

<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>
</body>
</html>
