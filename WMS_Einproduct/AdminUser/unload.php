<?php
include 'db.php'; // Databaseverbinding

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['po_id']) && isset($_POST['quantity'])) {
    $po_id = $_POST['po_id']; // Verkrijg PO ID
    $quantity_unloaded = (int)$_POST['quantity']; // Verkrijg ingevoerde hoeveelheid

    try {
        // Haal de PO-informatie op, inclusief de huidige hoeveelheid
        $po_query = "SELECT po_number, location, quantity FROM purchase_orders WHERE id = :po_id AND status = 'open'";
        $po_stmt = $conn->prepare($po_query);
        $po_stmt->bindParam(':po_id', $po_id);
        $po_stmt->execute();
        $po = $po_stmt->fetch(PDO::FETCH_ASSOC);

        if ($po) {
            $po_number = $po['po_number'];
            $location = $po['location'];
            $quantity = $po['quantity']; // Huidige voorraad in PO

            // Controleer of de hoeveelheid die geunload moet worden kleiner of gelijk is aan de huidige hoeveelheid
            if ($quantity_unloaded > $quantity) {
                echo "Fout: De hoeveelheid die je probeert te unloaden is groter dan de voorraad in de PO.";
                exit;
            }

            // Verplaats de geunloade producten naar de unloaded_stock tabel
            $unload_query = "INSERT INTO unloaded_stock (po_id, product_name, quantity, location)
                             VALUES (:po_id, :product_name, :quantity, :location)";
            $unload_stmt = $conn->prepare($unload_query);
            $unload_stmt->bindParam(':po_id', $po_id);
            $unload_stmt->bindParam(':product_name', $po_number);
            $unload_stmt->bindParam(':quantity', $quantity_unloaded);
            $unload_stmt->bindParam(':location', $location);
            $unload_stmt->execute();

            // Werk de hoeveelheid in de PO bij
            $update_po_query = "UPDATE purchase_orders SET quantity = quantity - :quantity_unloaded WHERE id = :po_id";
            $update_po_stmt = $conn->prepare($update_po_query);
            $update_po_stmt->bindParam(':quantity_unloaded', $quantity_unloaded);
            $update_po_stmt->bindParam(':po_id', $po_id);
            $update_po_stmt->execute();

            echo "Producten succesvol unloaded en opgeslagen in de unloaded_stock tabel!";
        } else {
            echo "Geen openstaande PO gevonden voor het opgegeven ID.";
        }
    } catch (Exception $ex) {
        echo "Er ging iets fout: " . $ex->getMessage();
    }
}
?>

<!-- HTML-formulier om producten te unloaden -->
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unload Producten</title>
</head>
<body>
<h1>Unload Producten van PO</h1>
<form method="POST" action="unload.php">
    <label for="po_id">PO ID:</label>
    <input type="number" name="po_id" required><br><br>

    <label for="quantity">Aantal Geunloade Producten:</label>
    <input type="number" name="quantity" required><br><br>

    <button type="submit">Unload</button>
</form>
</body>
</html>
