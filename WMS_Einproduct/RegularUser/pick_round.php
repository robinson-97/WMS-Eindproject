<?php
session_start();

// Controleer of de gebruiker is ingelogd als admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Je moet ingelogd zijn als admin om toegang te krijgen tot deze pagina.');
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $round_name = $_POST['round_name'];

    try {
        // Maak de picking ronde aan
        $sql = "INSERT INTO picking_rounds (round_name, start_date) VALUES (:round_name, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':round_name', $round_name);
        $stmt->execute();

        echo "Picking ronde succesvol aangemaakt!";
    } catch (PDOException $e) {
        echo "Fout bij het aanmaken van de picking ronde: " . $e->getMessage();
    }
}
?>

<form method="POST" action="pick_round.php">
    <label for="round_name">Ronde Naam:</label>
    <input type="text" id="round_name" name="round_name" required>
    <button type="submit">Maak Picking Ronde</button>
</form>
