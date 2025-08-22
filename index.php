<?php
require_once 'config.php';
require_once 'data_loader.php';

$data = load_inventory_data();

// Get page number from query
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Reset filters unless explicitly set
$factory = $_GET['factory'] ?? '';
$location = $_GET['location'] ?? '';
$needle = $_GET['needle'] ?? '';

// Apply filters
$filtered = array_filter($data, function($row) use ($factory, $location, $needle) {
    $matchFactory = $factory === '' || stripos($row['factory'], $factory) !== false;
    $matchLocation = $location === '' || stripos($row['stock location'], $location) !== false;
    $matchNeedle = $needle === '' || stripos($row['needle id'], $needle) !== false;
    return $matchFactory && $matchLocation && $matchNeedle;
});

// Pagination setup
$total_pages = ceil(count($filtered) / PER_PAGE);
$start = ($page - 1) * PER_PAGE;
$paginated = array_slice(array_values($filtered), $start, PER_PAGE);

// Get unique values for dropdowns
$factories = array_unique(array_column($data, 'factory'));
$locations = array_unique(array_column($data, 'stock location'));
$needle_ids = array_unique(array_column($data, 'needle id'));
sort($factories);
sort($locations);
sort($needle_ids);

// Build query string for export and pagination
$query_string = http_build_query([
    'page' => $page,
    'factory' => $factory,
    'location' => $location,
    'needle' => $needle
]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Needle Inventory Dashboard</title>
    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body>
<div class="container">
    <h1>Needle Inventory Dashboard</h1>
    <form method="get">
        <select name="factory" id="factory">
            <option value="">All Factories</option>
            <?php foreach ($factories as $f): ?>
                <option value="<?= htmlspecialchars($f) ?>" <?= $f === $factory ? 'selected' : '' ?>><?= htmlspecialchars($f) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="location" id="location">
            <option value="">All Locations</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= htmlspecialchars($loc) ?>" <?= $loc === $location ? 'selected' : '' ?>><?= htmlspecialchars($loc) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="needle" id="needle">
            <option value="">All Needle IDs</option>
            <?php foreach ($needle_ids as $nid): ?>
                <option value="<?= htmlspecialchars($nid) ?>" <?= $nid === $needle ? 'selected' : '' ?>><?= htmlspecialchars($nid) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Apply Filters</button>
        <a href="index.php"><button type="button">Clear Filters</button></a>
    </form>

    <div>
        <a href="export_excel.php?<?= $query_string ?>"><button>Export Excel</button></a>
        <a href="export_pdf.php?<?= $query_string ?>"><button>Export PDF</button></a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Factory</th>
                <th>Stock Location</th>
                <th>Needle ID</th>
                <th>Designation</th>
                <th>Minimum Stock</th>
                <th>Current Stock</th>
                <th>Target Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paginated as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['factory']) ?></td>
                    <td><?= htmlspecialchars($row['stock location']) ?></td>
                    <td><?= htmlspecialchars($row['needle id']) ?></td>
                    <td><?= htmlspecialchars($row['designation']) ?></td>
                    <td><?= htmlspecialchars($row['minimum stock level']) ?></td>
                    <td><?= htmlspecialchars($row['current stock level']) ?></td>
                    <td><?= htmlspecialchars($row['target stock level']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= $query_string ?>&page=<?= $page - 1 ?>">Previous</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <a href="?<?= $query_string ?>&page=<?= $p ?>" <?= $p === $page ? 'style="font-weight:bold;"' : '' ?>><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?<?= $query_string ?>&page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#factory').select2();
    $('#location').select2();
    $('#needle').select2();
});
</script>

</body>
</html>