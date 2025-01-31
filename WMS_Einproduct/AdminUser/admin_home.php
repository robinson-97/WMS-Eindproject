<?php

include 'db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS Admin Dashboard</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/AdminUser/admin_home.css"> <!-- Verbind de aangepaste CSS voor het dashboard -->
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="../index.html">Home</a>
            <a href="../index.html">Uitloggen</a>
        </nav>
    </div>
</header>

<main class="container">
    <div class="row">
        <div class="col left-column">
            <div class="content-section">
                <h2>Daily News</h2>
                <div class="news-item">
                    <h5 class="news-title">Article Title 1</h5>
                    <p class="news-summary">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed feugiat
                        semper libero, id hendrerit odio efficitur at.</p>
                    <a href="#" class="news-link">Read More</a>
                </div>
                <div class="news-item">
                    <h5 class="news-title">Article Title 2</h5>
                    <p class="news-summary">Nulla facilisi. Vivamus ac ex ac elit eleifend tristique id vel diam.</p>
                    <a href="#" class="news-link">Read More</a>
                </div>
            </div>
        </div>
        <div class="col right-column">
            <div class="content-section">
                <h2>Important Updates</h2>
                <ul class="updates-list">
                    <li>
                        <span class="update-date">2023-11-15</span> - New feature added: Inventory forecasting.
                    </li>
                    <li>
                        <span class="update-date">2023-11-12</span> - System maintenance scheduled for this weekend.
                    </li>
                    <li>
                        <span class="update-date">2023-11-10</span> - Resolved issue with order processing delays.
                    </li>
                </ul>
                <!-- Button naar admin_dashboard.php -->
                <a href="admin_dashboard.php" class="btn btn-primary">Go to Admin Dashboard</a>
            </div>
        </div>
    </div>
</main>
<footer class="footer">
    <div class="footer-container">
        <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
        <p>
            <a href="privacy_policy.php">Privacybeleid</a> |
            <a href="terms_of_service.php">Algemene Voorwaarden</a>
        </p>
    </div>
</footer>
</body>
</html>
