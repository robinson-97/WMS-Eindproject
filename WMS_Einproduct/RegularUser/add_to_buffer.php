<?php
include 'db.php'; // Databaseverbinding

// Verkrijg bufferlocaties uit de database
try {
    $buffer_query = "SELECT id, name FROM buffer_locations";
    $stmt = $conn->prepare($buffer_query);
    $stmt->execute();
    $buffers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Fout bij het ophalen van bufferlocaties: " . htmlspecialchars($e->getMessage());
    exit;
}

// Verkrijg producten uit de `unloaded_stock` tabel
try {
    $product_query = "SELECT product_name, quantity FROM unloaded_stock WHERE quantity > 0";
    $product_stmt = $conn->prepare($product_query);
    $product_stmt->execute();
    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Fout bij het ophalen van producten: " . htmlspecialchars($e->getMessage());
    exit;
}

// Verwerk formulier indien verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $quantity = intval($_POST['quantity']);
    $buffer_id = intval($_POST['buffer_id']);

    try {
        // Controleer of het product in `unloaded_stock` bestaat en voldoende voorraad heeft
        $check_query = "SELECT quantity FROM unloaded_stock WHERE product_name = :product_name";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':product_name', $product_name);
        $check_stmt->execute();
        $stock = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($stock && $stock['quantity'] >= $quantity) {
            // Voeg product toe aan buffer_stock
            $insert_query = "INSERT INTO buffer_stock (product_name, quantity, buffer_location_id) 
                             VALUES (:product_name, :quantity, :buffer_id)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bindParam(':product_name', $product_name);
            $insert_stmt->bindParam(':quantity', $quantity);
            $insert_stmt->bindParam(':buffer_id', $buffer_id);
            $insert_stmt->execute();

            // Verminder de voorraad in `unloaded_stock`
            $update_query = "UPDATE unloaded_stock SET quantity = quantity - :quantity WHERE product_name = :product_name";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bindParam(':quantity', $quantity);
            $update_stmt->bindParam(':product_name', $product_name);
            $update_stmt->execute();

            echo "Product succesvol toegevoegd aan buffer!";
        } else {
            echo "Onvoldoende voorraad in `unloaded_stock` of product niet beschikbaar.";
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
    <title>Product Toevoegen aan Buffer</title>
</head>
<body>
<h1>Product Toevoegen aan Buffer</h1>
<form method="POST" action="add_to_buffer.php">
    <label for="product_name">Selecteer Product:</label>
    <select id="product_name" name="product_name" required>
        <option value="">Selecteer een product</option>
        <?php foreach ($products as $product): ?>
            <option value="<?= htmlspecialchars($product['product_name']) ?>">
                <?= htmlspecialchars($product['product_name']) ?> (Beschikbaar: <?= htmlspecialchars($product['quantity']) ?>)
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="quantity">Aantal:</label>
    <input type="number" id="quantity" name="quantity" min="1" required><br><br>

    <label for="buffer_id">Bufferlocatie:</label>
    <select id="buffer_id" name="buffer_id" required>
        <option value="">Selecteer een bufferlocatie</option>
        <?php foreach ($buffers as $buffer): ?>
            <option value="<?= htmlspecialchars($buffer['id']) ?>"><?= htmlspecialchars($buffer['name']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Toevoegen</button>
</form>
</body>
</html>
