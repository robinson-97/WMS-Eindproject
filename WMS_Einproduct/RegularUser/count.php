<?php
include 'db.php'; // Verbind met de database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Stap 3: Update het aantal in de database
    if (isset($_POST['location']) && isset($_POST['product']) && isset($_POST['count_adjustment'])) {
        $location = $_POST['location'];
        $product = $_POST['product'];
        $count_adjustment = (int)$_POST['count_adjustment'];

        try {
            // Update query
            $update_query = "
                UPDATE stock
                SET quantity = :count_adjustment 
                WHERE location = :location AND product_name = :product
            ";
            $stmt = $conn->prepare($update_query);
            $stmt->bindParam(':count_adjustment', $count_adjustment);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':product', $product);
            $stmt->execute();

            echo "Aantal succesvol bijgewerkt!";
        } catch (Exception $ex) {
            echo "Fout bij het bijwerken: " . $ex->getMessage();
        }
    }
}

// Stap 2: Haal producten op voor een geselecteerde locatie
$products = [];
if (isset($_GET['location'])) {
    $location = $_GET['location'];
    try {
        $product_query = "
            SELECT product_name, quantity 
            FROM stock 
            WHERE location = :location
        ";
        $stmt = $conn->prepare($product_query);
        $stmt->bindParam(':location', $location);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        echo "Fout bij het ophalen van producten: " . $ex->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locatie Count</title>
    <style>
        form {
            margin: 20px auto;
            width: 50%;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1 style="text-align: center;">Voorraad Count Systeem</h1>

<!-- Stap 1: Locatie Invoeren -->
<form method="GET" action="">
    <label for="location">Locatie:</label>
    <input type="text" name="location" id="location" required>
    <button type="submit">Bekijk Producten</button>
</form>

<?php if (!empty($products)): ?>
    <!-- Stap 2 & 3: Product Selecteren en Aanpassen -->
    <form method="POST" action="">
        <input type="hidden" name="location" value="<?= htmlspecialchars($location) ?>">
        <table>
            <thead>
            <tr>
                <th>Product</th>
                <th>Huidige Hoeveelheid</th>
                <th>Nieuwe Hoeveelheid</th>
                <th>Actie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <input type="radio" name="product" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                        <?= htmlspecialchars($product['product_name']) ?>
                    </td>
                    <td><?= (int)$product['quantity'] ?></td>
                    <td>
                        <input type="number" name="count_adjustment" min="0" required>
                    </td>
                    <td>
                        <button type="submit">Aanpassen</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>
<?php elseif (isset($_GET['location'])): ?>
    <p style="text-align: center;">Geen producten gevonden voor locatie: <?= htmlspecialchars($location) ?></p>
<?php endif; ?>
</body>
</html>
