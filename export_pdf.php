<?php
require_once 'config.php';
require_once 'data_loader.php';
require_once 'exporter.php';

$data = load_inventory_data();

$factory = $_GET['factory'] ?? '';
$location = $_GET['location'] ?? '';
$needle = $_GET['needle'] ?? '';

$filtered = array_filter($data, function($row) use ($factory, $location, $needle) {
    $matchFactory = $factory === '' || stripos($row['factory'], $factory) !== false;
    $matchLocation = $location === '' || stripos($row['stock location'], $location) !== false;
    $matchNeedle = $needle === '' || stripos($row['needle id'], $needle) !== false;
    return $matchFactory && $matchLocation && $matchNeedle;
});

export_pdf(array_values($filtered));
?>
