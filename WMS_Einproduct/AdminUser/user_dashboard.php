<?php session_start(); ?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
</head>
<body>
<h1>Welkom op de Website</h1>

<ul>
    <li><a href="home.php">Home</a></li>
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <!-- Admin menu-opties -->
        <li><a href="create_po.php">Create PO</a></li>
        <li><a href="po_list.php">PO List</a></li>
        <li><a href="create_buffer.php">Create Buffer</a></li>
        <li><a href="create_user.php">Gebruiker Aanmaken</a></li>
    <?php elseif (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
        <!-- Normale gebruiker menu-opties -->
        <li><a href="recieve.php">Replenish</a></li>
        <li><a href="add_to_buffer.php">Add to buffer</a></li>
        <li><a href="stock_info.php">Stock info</a></li>
        <li><a href="count.php.php">Count</a></li>



    <?php endif; ?>
    <li><a href="logout.php">Uitloggen</a></li>
</ul>
</body>
</html>
