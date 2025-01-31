<?php
include 'db.php';  // Zorg ervoor dat je db.php correct is ingesteld voor de databaseverbinding


session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Verwijs naar de inlogpagina
    exit;
}


// Controleer of het formulier is ingediend en de PO_id aanwezig is
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['po_id']) && isset($_POST['unload_quantity'])) {
    $po_id = $_POST['po_id'];
    $unload_quantity = $_POST['unload_quantity']; // Verkrijg het aantal producten om te unloaden

    try {
        // Haal de PO-informatie op, inclusief de hoeveelheid
        $po_query = "SELECT quantity, po_number FROM purchase_orders WHERE id = :po_id";
        $po_stmt = $conn->prepare($po_query);
        $po_stmt->bindParam(':po_id', $po_id);
        $po_stmt->execute();
        $po = $po_stmt->fetch(PDO::FETCH_ASSOC);

        if ($po) {
            $current_quantity = $po['quantity'];
            // Controleer of de hoeveelheid die we willen unloaden niet groter is dan de beschikbare hoeveelheid
            if ($unload_quantity > 0 && $unload_quantity <= $current_quantity) {
                // Update de hoeveelheid in de purchase order
                $new_quantity = $current_quantity - $unload_quantity;
                $update_po_query = "UPDATE purchase_orders SET quantity = :new_quantity WHERE id = :po_id";
                $update_po_stmt = $conn->prepare($update_po_query);
                $update_po_stmt->bindParam(':new_quantity', $new_quantity);
                $update_po_stmt->bindParam(':po_id', $po_id);
                $update_po_stmt->execute();

                // Voeg de unload hoeveelheid toe aan de voorraad
                $stock_query = "UPDATE stock SET quantity = quantity - :unload_quantity WHERE product_name = :po_number";
                $stock_stmt = $conn->prepare($stock_query);
                $stock_stmt->bindParam(':unload_quantity', $unload_quantity);
                $stock_stmt->bindParam(':po_number', $po['po_number']); // Gebruik PO-nummer als productnaam
                $stock_stmt->execute();

                // Voeg de geünloade producten toe aan de unloaded_stock tabel
                $unload_stock_query = "INSERT INTO unloaded_stock (po_id, product_name, quantity) VALUES (:po_id, :po_number, :unload_quantity)";
                $unload_stock_stmt = $conn->prepare($unload_stock_query);
                $test = $po['po_number'];
                $unload_stock_stmt->bindParam(':po_id', $po_id);
                $unload_stock_stmt->bindParam(':po_number', $test);
                $unload_stock_stmt->bindParam(':unload_quantity', $unload_quantity);
                $unload_stock_stmt->execute();

                // Verwijder de PO uit de lijst als de hoeveelheid op is
                if ($new_quantity == 0) {
                    $delete_po_query = "DELETE FROM purchase_orders WHERE id = :po_id";
                    $delete_po_stmt = $conn->prepare($delete_po_query);
                    $delete_po_stmt->bindParam(':po_id', $po_id);
                    $delete_po_stmt->execute();
                }

                echo "Succesvol " . $unload_quantity . " producten unloaded!";
            } else {
                echo "Ongeldige hoeveelheid om te unloaden (maximaal " . $current_quantity . " beschikbaar).";
            }
        } else {
            echo "PO niet gevonden.";
        }
    } catch (Exception $ex) {
        echo "Er is een fout opgetreden: " . $ex->getMessage();
    }
}

// Query om gesloten PO's op te halen
$sql = "SELECT id, po_number, created_at, status, quantity FROM purchase_orders WHERE status = 'closed'"; // Alleen gesloten PO's ophalen
$stmt = $conn->prepare($sql);
$stmt->execute();

// Haal alle resultaten op
$poList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Openstaande PO's</title>
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
<h1 style="text-align: center;">Geëxporteerde Purchase Orders (PO's)</h1>
<table>
    <thead>
    <tr>
        <th>PO Nummer</th>
        <th>Datum Aangemaakt</th>
        <th>Status</th>
        <th>Hoeveelheid</th>
        <th>Actie</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // Controleer of er resultaten zijn
    if (count($poList) > 0) {
        // Resultaten doorlopen
        foreach ($poList as $row) {
            echo "<tr>";
            echo "<td>" . $row['po_number'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            // Voeg de "Unload" knop en de hoeveelheid toe
            echo "<td>
                    <form method='POST' action='po_list.php'>
                        <input type='hidden' name='po_id' value='" . $row['id'] . "'>
                        <input type='number' name='unload_quantity' min='1' max='" . $row['quantity'] . "' required>
                        <button type='submit'>Unload</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Geen gesloten PO's gevonden.</td></tr>";
    }
    ?>
    </tbody>
</table>
</body>
</html>