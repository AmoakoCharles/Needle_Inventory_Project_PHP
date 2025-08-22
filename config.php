<?php
if (!defined('EXCEL_FILE')) {
    define('EXCEL_FILE', 'needle-inventory.xlsx');
}
if (!defined('PER_PAGE')) {
    define('PER_PAGE', 20);
}
if (!defined('REQUIRED_COLUMNS')) {
    define('REQUIRED_COLUMNS', [
        'factory', 'stock location', 'needle id', 'designation',
        'minimum stock level', 'current stock level', 'target stock level'
    ]);
}
?>
