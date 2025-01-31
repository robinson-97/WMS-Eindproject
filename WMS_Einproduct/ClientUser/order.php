<?php
include 'db.php';
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    die("Je moet ingelogd zijn om een bestelling te plaatsen.");
}

// Haal producten op die op voorraad zijn
try {
    $sql = "SELECT * FROM stock WHERE quantity > 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij het ophalen van producten: " . htmlspecialchars($e->getMessage()));
}

// Verwerk bestelling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($product_id && $quantity && $quantity > 0) {
        try {
            // Begin een transactie om racecondities te voorkomen
            $conn->beginTransaction();

            // Controleer of er voldoende voorraad is
            $sql = "SELECT * FROM stock WHERE id = :product_id FOR UPDATE";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && $product['quantity'] >= $quantity) {
                // Voeg bestelling toe aan de database
                $order_sql = "INSERT INTO clientorders (user_id, product_id, quantity, order_date) VALUES (:user_id, :product_id, :quantity, NOW())";
                $order_stmt = $conn->prepare($order_sql);
                $order_stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $order_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $order_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                if ($order_stmt->execute()) {
                    // Verminder de voorraad
                    $update_sql = "UPDATE stock SET quantity = quantity - :quantity WHERE id = :product_id";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                    $update_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $update_stmt->execute();

                    // Bevestig transactie
                    $conn->commit();
                    echo "<p class='success-message'>Bestelling succesvol geplaatst!</p>";
                } else {
                    $conn->rollBack();
                    echo "<p class='error-message'>Fout bij het plaatsen van de bestelling.</p>";
                }
            } else {
                $conn->rollBack();
                echo "<p class='error-message'>Onvoldoende voorraad voor het gekozen product.</p>";
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "<p class='error-message'>Fout: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error-message'>Ongeldige invoer. Controleer of alle velden correct zijn ingevuld.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellen</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/ClientUser/order.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="../index.html">Home</a>
            <a href="logout.php">Uitloggen</a>
        </nav>
    </div>
</header>

<section class="order-section">
    <div class="container">
        <h1>Bestel Producten</h1>

        <form method="POST" action="order.php">
            <label for="product_id">Product:</label>
            <select name="product_id" id="product_id" required>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['product_name']); ?> (Beschikbaar: <?php echo $product['quantity']; ?>)
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option disabled>Geen producten beschikbaar</option>
                <?php endif; ?>
            </select>

            <label for="quantity">Aantal:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <button type="submit">Bestel</button>
        </form>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 RRM Warehouses. Alle rechten voorbehouden.</p>
</footer>
</body>
</html>
