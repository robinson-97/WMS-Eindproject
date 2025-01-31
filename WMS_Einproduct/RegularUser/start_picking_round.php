<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Je moet ingelogd zijn als admin om toegang te krijgen tot deze pagina.');
}

// Haal picking rondes op voor het <select>-element
try {
    $sql = "SELECT id, round_name, status FROM picking_rounds WHERE status = 'pending' ORDER BY start_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rounds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Fout bij het ophalen van picking rondes: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $round_id = $_POST['round_id']; // picking ronde id

    try {
        // Zet de picking ronde status naar 'in_progress'
        $sql = "UPDATE picking_rounds SET status = 'in_progress', start_date = NOW() WHERE id = :round_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':round_id', $round_id);
        $stmt->execute();

        // Haal gegevens van picked_orders op voor de geselecteerde ronde
        $items_sql = "
            SELECT po.product_id, po.quantity, l.name AS location_name
            FROM picked_orders po
            JOIN locations l ON po.location_id = l.id
            WHERE po.picking_round_id = :round_id
        ";
        $items_stmt = $conn->prepare($items_sql);
        $items_stmt->bindParam(':round_id', $round_id);
        $items_stmt->execute();
        $picked_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Fout bij het starten van de picking ronde: " . $e->getMessage());
    }
}
?>

<form method="POST" action="start_picking_round.php">
    <label for="round_id">Picking Ronde:</label>
    <select name="round_id" id="round_id" required>
        <option value="">-- Selecteer een ronde --</option>
        <?php foreach ($rounds as $round): ?>
            <option value="<?php echo $round['id']; ?>">
                <?php echo htmlspecialchars($round['round_name']); ?> - Status: <?php echo htmlspecialchars($round['status']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Start Picking Ronde</button>
</form>

<?php if (!empty($picked_items)): ?>
    <h2>Picking Ronde Actief</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
        <tr>
            <th>Item ID</th>
            <th>Hoeveelheid</th>
            <th>Locatie</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($picked_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td style="font-size: 20px; font-weight: bold;"><?php echo htmlspecialchars($item['location_name']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p>Er zijn geen items in deze picking ronde.</p>
<?php endif; ?>
