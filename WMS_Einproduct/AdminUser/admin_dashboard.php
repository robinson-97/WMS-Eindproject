<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/AdminUser/admin_dashboard.css">
</head>
<body>

<!-- Navbar -->
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="admin_home.php">Home</a>
            <a href="logout.php">Uitloggen</a>
        </nav>
    </div>
</header>

<!-- Hoofdinhoud van het dashboard -->
<main class="dashboard">
    <div class="button-grid">
        <a href="create_po.php" class="dashboard-button">Create PO</a>
        <a href="create_users.php" class="dashboard-button">Create Users</a>
        <a href="create_locations.php" class="dashboard-button">Create Locations</a>
        <a href="po_list.php" class="dashboard-button">PO List</a>
        <a href="recieve.php" class="dashboard-button">Receive</a>
        <a href="create_buffer.php" class="dashboard-button">Create Buffer</a>
        <a href="count.php" class="dashboard-button">Count</a>
        <a href="add_to_buffer.php" class="dashboard-button">Add to Buffer</a>
        <a href="stock_info.php" class="dashboard-button">Stock Info</a>
        <a href="pick_round.php" class="dashboard-button">Pick Round</a>
        <a href="start_picking_round.php" class="dashboard-button">Start Picking Round</a>
        <a href="view_rounds.php" class="dashboard-button">View Rounds</a>
        <a href="logout.php" class="dashboard-button logout-button">Uitloggen</a>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>

</body>
</html>
