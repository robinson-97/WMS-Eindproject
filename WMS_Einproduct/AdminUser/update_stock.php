<?php
include 'db.php'; // Zorg voor databaseverbinding

$message = ''; // Voor succes- of foutmeldingen

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Verkrijg het ID van de voorraad
    $quantity = $_POST['quantity']; // Nieuwe hoeveelheid

    try {
        // SQL-query om de voorraad te updaten
        $sql = "UPDATE stock SET quantity = quantity + :quantity, updated_at = NOW() WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $message = "Voorraad succesvol geüpdatet!";
        } else {
            $message = "Er is een fout opgetreden bij het updaten van de voorraad.";
        }
    } catch (Exception $ex) {
        $message = 'Er ging iets fout: ' . $ex->getMessage();
    }
}

// Haal de volledige voorraad op
try {
    $sql = "SELECT id, product_name, quantity, updated_at FROM stock";
    $stmt = $conn->query($sql);
    $stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    die('Er ging iets fout bij het ophalen van de voorraad: ' . $ex->getMessage());
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Stock</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/AdminUser/update_stock.css">
</head>
<body>
<nav class="navbar">
    <h1>WMS Admin</h1>
    <div>
        <a href="admin_home.php">Home</a>
        <a href="stock_info.php">Stock Info</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</nav>

<main class="update-stock-container">
    <h2>Update Voorraad</h2>

    <!-- Succes- of foutmelding -->
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Formulier om voorraad bij te werken -->
    <form method="POST" action="update_stock.php" class="update-form">
        <label for="id">Product ID:</label>
        <input type="number" id="id" name="id" placeholder="Vul product ID in" required>

        <label for="quantity">Hoeveelheid:</label>
        <input type="number" id="quantity" name="quantity" placeholder="Vul hoeveelheid in" required>

        <button type="submit" class="update-button">Update Voorraad</button>
    </form>

    <!-- Voorraad tabel -->
    <h2>Huidige Voorraad</h2>
    <div class="stock-table-container">
        <table class="stock-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Productnaam</th>
                <th>Hoeveelheid</th>
                <th>Laatst Bijgewerkt</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stock_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td><?= htmlspecialchars($item['updated_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<footer class="footer">
    <p>&copy; 2025 WMS Admin Dashboard</p>
</footer>
</body>
</html>
