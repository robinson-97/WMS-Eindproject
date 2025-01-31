<?php
include 'db.php';

$query = $conn->query("SELECT location, product_name, quantity FROM stock");
$data = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
