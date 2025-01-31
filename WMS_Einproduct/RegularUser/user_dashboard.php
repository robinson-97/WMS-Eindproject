<?php session_start(); ?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/AdminUser/admin_dashboard.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="../index.html">Home</a>
            <a href="logout.php">Uitloggen</a>
        </nav>
    </div>
</header>

<section class="menu-section">
    <div class="container">
        <h1>Welkom op de Website</h1>

        <div class="button-grid">
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <!-- Admin menu-opties -->
                <a href="../AdminUser/create_po.php" class="dashboard-button">Create PO</a>
                <a href="../AdminUser/po_list.php" class="dashboard-button">PO List</a>
                <a href="../AdminUser/create_buffer.php" class="dashboard-button">Create Buffer</a>
                <a href="../AdminUser/create_user.php" class="dashboard-button">Gebruiker Aanmaken</a>
            <?php elseif (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <!-- Normale gebruiker menu-opties -->
                <a href="../AdminUser/recieve.php" class="dashboard-button">Replenish</a>
                <a href="../AdminUser/add_to_buffer.php" class="dashboard-button">Add to buffer</a>
                <a href="../AdminUser/stock_info.php" class="dashboard-button">Stock info</a>
                <a href="../AdminUser/count.php.php" class="dashboard-button">Count</a>
            <?php endif; ?>
            <a href="logout.php" class="dashboard-button logout-button">Uitloggen</a>
        </div>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 RRM Warehouses. Alle rechten voorbehouden.</p>
</footer>
</body>
</html>
