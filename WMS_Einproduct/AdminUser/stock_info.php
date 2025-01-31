<?php
include 'db.php'; // Zorg voor databaseverbinding

$searchResult = [];
$searchType = '';
$searchTerm = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_type']) && isset($_GET['search_term'])) {
    $searchType = $_GET['search_type'];
    $searchTerm = trim($_GET['search_term']);

    try {
        if ($searchType === 'location') {
            $query = "SELECT product_name, quantity, location FROM stock WHERE location = :search_term";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':search_term', $searchTerm);
        } elseif ($searchType === 'product') {
            $query = "SELECT DISTINCT location, quantity FROM stock WHERE product_name = :search_term";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':search_term', $searchTerm);
        }
        $stmt->execute();
        $searchResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        echo "Er ging iets fout: " . $ex->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Informatie</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Verbind de algemene main.css -->
    <link rel="stylesheet" href="../css/AdminUser/stock_info.css"> <!-- Verbind de pagina-specifieke CSS -->
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>RRM Warehouses</h1>
        <nav>
            <a href="admin_dashboard.php">Home</a>
        </nav>
    </div>
</header>


<main class="container">
    <h1 class="page-title">Stock Informatie</h1>
    <form method="GET" action="stock_info.php" class="search-form">
        <div class="form-group">
            <label for="search_type">Selecteer een zoekoptie:</label>
            <select name="search_type" id="search_type" class="form-control" required>
                <option value="location" <?= $searchType === 'location' ? 'selected' : '' ?>>Zoeken op Locatie</option>
                <option value="product" <?= $searchType === 'product' ? 'selected' : '' ?>>Zoeken op Product</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="search_term" class="form-control" placeholder="Voer een zoekterm in" value="<?= htmlspecialchars($searchTerm) ?>" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-search">Zoeken</button>
        </div>
    </form>

    <?php if (!empty($searchResult)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <?php if ($searchType === 'location'): ?>
                        <th>Product</th>
                        <th>Hoeveelheid</th>
                        <th>Locatie</th>
                    <?php elseif ($searchType === 'product'): ?>
                        <th>Locatie</th>
                        <th>Hoeveelheid</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($searchResult as $row): ?>
                    <tr>
                        <?php if ($searchType === 'location'): ?>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                        <?php elseif ($searchType === 'product'): ?>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($searchTerm)): ?>
            <p class="no-results">Geen resultaten gevonden voor "<?= htmlspecialchars($searchTerm) ?>".</p>
        <?php endif; ?>
    <?php endif; ?>
</main>

<footer class="footer">
    <p>&copy; 2025 WMS Solutions. Alle rechten voorbehouden.</p>
    <p><a href="privacy_policy.php">Privacybeleid</a> | <a href="terms_of_service.php">Algemene Voorwaarden</a></p>
</footer>

</body>
</html>
