<?php
ob_start();
require_once 'config.php';
require_once 'data_loader.php';
require_once 'exporter.php';

// Load inventory data
$data = load_inventory_data();

// Apply filters from query string
$factory = $_GET['factory'] ?? '';
$location = $_GET['location'] ?? '';
$needle = $_GET['needle'] ?? '';

$filtered = array_filter($data, function($row) use ($factory, $location, $needle) {
    $matchFactory = $factory === '' || stripos($row['factory'], $factory) !== false;
    $matchLocation = $location === '' || stripos($row['stock location'], $location) !== false;
    $matchNeedle = $needle === '' || stripos($row['needle id'], $needle) !== false;
    return $matchFactory && $matchLocation && $matchNeedle;
});

// Export to Excel
export_excel(array_values($filtered));
ob_end_flush();
?>
