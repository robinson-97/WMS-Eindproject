<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); // Redirect to admin login page
    exit(); // Stop script execution after redirection
}

$sql = "SELECT * FROM picking_rounds ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rounds = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bekijk Picking Rondes</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/AdminUser/view_rounds.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="admin_home.php">Home</a>
        </nav>
    </div>
</header>

<main class="round-list-container">
    <h1 class="round-list-title">Overzicht van Picking Rondes</h1>
    <?php if (!empty($rounds)):?>
        <table>
            <thead>
            <tr>
                <th>Ronde Naam</th>
                <th>Start Datum</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rounds as $round):?>
                <tr>
                    <td><?= htmlspecialchars($round['round_name'])?></td>
                    <td><?= htmlspecialchars($round['start_date'])?></td>
                    <td><?= htmlspecialchars($round['status'])?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    <?php else:?>
        <p>Geen picking rondes gevonden.</p>
    <?php endif;?>
</main>

<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>
</body>
</html>
