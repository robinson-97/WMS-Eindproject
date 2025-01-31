// view_rounds.php
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Je moet ingelogd zijn als admin om toegang te krijgen tot deze pagina.');
}

$sql = "SELECT * FROM picking_rounds ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rounds = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Overzicht van Picking Ronden</h1>";
echo "<ul>";
    foreach ($rounds as $round) {
    echo "<li>" . $round['round_name'] . " - Status: " . $round['status'] . "</li>";
    }
    echo "</ul>";
?>