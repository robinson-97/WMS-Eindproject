<?php
include 'db.php'; // Verbind met de database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Replenish-functionaliteit
    if (isset($_POST['action']) && $_POST['action'] === 'replenish') {
        $po_id = $_POST['po_id'];
        $quantity = (int)$_POST['quantity'];

        try {
            // Haal de hoeveelheid beschikbare producten in de unloaded_stock tabel
            $unloaded_query = "
                SELECT SUM(quantity) AS total_unloaded 
                FROM unloaded_stock 
                WHERE po_id = :po_id
            ";
            $unloaded_stmt = $conn->prepare($unloaded_query);
            $unloaded_stmt->bindParam(':po_id', $po_id);
            $unloaded_stmt->execute();
            $unloaded = $unloaded_stmt->fetch(PDO::FETCH_ASSOC);
            $total_unloaded = (int)($unloaded['total_unloaded'] ?? 0);

            if ($total_unloaded === 0) {
                echo "Fout: Geen producten beschikbaar om te ontvangen.";
            } elseif ($quantity > $total_unloaded) {
                echo "Fout: Je probeert meer producten te ontvangen dan er beschikbaar zijn.";
            } else {
                // Update de voorraad in de stock tabel
                $stock_query = "
                    INSERT INTO stock (product_name, location, quantity)
                    VALUES (:product_name, :location, :quantity)
                    ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
                ";
                $stock_stmt = $conn->prepare($stock_query);

                // Haal de PO-informatie op (zoals productnaam en locatie)
                $po_query = "
                    SELECT po_number, location 
                    FROM purchase_orders 
                    WHERE id = :po_id
                ";
                $po_stmt = $conn->prepare($po_query);
                $po_stmt->bindParam(':po_id', $po_id);
                $po_stmt->execute();
                $po = $po_stmt->fetch(PDO::FETCH_ASSOC);

                $po_number = $po['po_number'] ?? 'Onbekend';
                $location = $po['location'] ?? 'Onbekend';

                $stock_stmt->bindParam(':product_name', $po_number);
                $stock_stmt->bindParam(':location', $location);
                $stock_stmt->bindParam(':quantity', $quantity);
                $stock_stmt->execute();

                // Update de hoeveelheid in de unloaded_stock tabel
                $update_unloaded_query = "
                    UPDATE unloaded_stock 
                    SET quantity = quantity - :quantity 
                    WHERE po_id = :po_id AND quantity >= :quantity
                ";
                $update_unloaded_stmt = $conn->prepare($update_unloaded_query);
                $update_unloaded_stmt->bindParam(':quantity', $quantity);
                $update_unloaded_stmt->bindParam(':po_id', $po_id);
                $update_unloaded_stmt->execute();

                echo "Product succesvol ontvangen!";
            }
        } catch (Exception $ex) {
            echo "Er ging iets fout: " . $ex->getMessage();
        }
    }
}

// Haal alle unieke PO's op uit de unloaded_stock-tabel
$sql = "
    SELECT us.po_id, po.po_number, po.created_at, SUM(us.quantity) AS total_unloaded 
    FROM unloaded_stock us
    JOIN purchase_orders po ON us.po_id = po.id
    GROUP BY us.po_id, po.po_number, po.created_at
    HAVING total_unloaded > 0
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$poList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recieve Producten</title>
    <style>
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
<h1 style="text-align: center;">Recieve Producten</h1>
<table>
    <thead>
    <tr>
        <th>PO Nummer</th>
        <th>Datum Aangemaakt</th>
        <th>Hoeveelheid</th>
        <th>Acties</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($poList)): ?>
        <?php foreach ($poList as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['po_number']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td><?= (int)$row['total_unloaded'] ?></td>
                <td>
                    <!-- Replenish Form -->
                    <form method="POST">
                        <input type="hidden" name="action" value="replenish">
                        <input type="hidden" name="po_id" value="<?= $row['po_id'] ?>">
                        <input type="number" name="quantity" placeholder="Aantal" required>
                        <button type="submit">Ontvang</button>
                    </form>

                    <!-- Button to Add to Buffer Locations -->
                    <form method="GET" action="add_to_buffer.php">
                        <input type="hidden" name="po_id" value="<?= $row['po_id'] ?>">
                        <button type="submit">Voeg toe aan Buffer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">Geen voorraad gevonden om te ontvangen.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
