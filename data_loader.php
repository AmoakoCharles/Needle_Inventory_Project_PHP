<?php
require 'vendor/autoload.php';
require 'config.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function load_inventory_data() {
    
    $spreadsheet = IOFactory::load(EXCEL_FILE);
    $allData = [];

    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        $rows = $sheet->toArray(null, true, true, true);

    $headers = [];
    foreach ($rows[1] as $colKey => $colName) {
    $headers[$colKey] = strtolower(trim($colName));
    }
    unset($rows[1]);


        foreach ($rows as $row) {
    $entry = [];
    foreach (REQUIRED_COLUMNS as $col) {
        $colKey = array_search(strtolower($col), $headers);
        if ($colKey !== false && isset($row[$colKey])) {
            $entry[$col] = $row[$colKey];
        } else {
            $entry[$col] = '';
        }
    }
    $allData[] = $entry;
    }

    }

    return $allData;
}
?>
