<?php
include 'db.php'; // Zorg voor databaseverbinding

$searchResult = [];
$searchType = '';
$searchTerm = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_type']) && isset($_GET['search_term'])) {
    $searchType = $_GET['search_type']; // Ophalen van zoektype
    $searchTerm = trim($_GET['search_term']); // Ophalen van zoekterm

    try {
        if ($searchType === 'location') {
            // Zoek producten op een specifieke locatie
            $query = "SELECT product_name, quantity, location FROM stock WHERE location = :search_term";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':search_term', $searchTerm);
        } elseif ($searchType === 'product') {
            // Zoek locaties voor een specifiek product
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        form {
            margin: 20px 0;
            text-align: center;
        }
        input[type="text"] {
            padding: 5px;
            width: 300px;
        }
        button {
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .message {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<h1 style="text-align: center;">Stock Informatie</h1>

<!-- Zoekformulier -->
<form method="GET" action="stock_info.php">
    <label for="search_type">Selecteer een zoekoptie:</label>
    <select name="search_type" id="search_type" required>
        <option value="location" <?= $searchType === 'location' ? 'selected' : '' ?>>Zoeken op Locatie</option>
        <option value="product" <?= $searchType === 'product' ? 'selected' : '' ?>>Zoeken op Product</option>
    </select>
    <br><br>
    <input type="text" name="search_term" placeholder="Voer een zoekterm in" value="<?= htmlspecialchars($searchTerm) ?>" required>
    <button type="submit">Zoeken</button>
</form>

<!-- Zoekresultaten -->
<?php if (!empty($searchResult)): ?>
    <table>
        <thead>
        <tr>
            <?php if ($searchType === 'location'): ?>
                <th>Product</th>
                <th>Hoeveelheid</th>
                <th>Locatie</th>
            <?php elseif ($searchType === 'product'): ?>
                <th>Locatie</th>
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
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($searchTerm)): ?>
        <p class="message">Geen resultaten gevonden voor "<?= htmlspecialchars($searchTerm) ?>".</p>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
