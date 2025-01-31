<?php
// pick_item.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
die('Je moet ingelogd zijn om te picken.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$picked_order_id = $_POST['picked_order_id'];

try {
// Markeer het product als gepickt
$sql = "UPDATE picked_orders SET status = 'picked', picked_at = NOW() WHERE id = :picked_order_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':picked_order_id', $picked_order_id);
$stmt->execute();

echo "Product succesvol gepickt!";
} catch (PDOException $e) {
echo "Fout bij het markeren van het product als gepickt: " . $e->getMessage();
}
}
?>

<form method="POST" action="pick_item.php">
    <label for="picked_order_id">Order ID:</label>
    <input type="text" id="picked_order_id" name="picked_order_id" required>

    <button type="submit">Markeer als Gepickt</button>
</form>
