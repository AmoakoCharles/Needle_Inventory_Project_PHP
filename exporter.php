<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//use FPDF;

function export_excel($data) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(array_keys($data[0]), null, 'A1');
    $sheet->fromArray($data, null, 'A2');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="inventory.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');

}

function export_pdf($data) {
    require_once('fpdf_folder\fpdf.php');
    $pdf = new FPDF('L', 'mm', 'A4'); // Landscape
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Define columns and widths
    $columns = [
        'stock location' => 60,
        'needle id' => 60,
        'minimum stock level' => 45,
        'current stock level' => 55,
        'target stock level' => 45
    ];

    // Table headers
    foreach ($columns as $header => $width) {
        $pdf->Cell($width, 10, ucfirst($header), 1, 0, 'C');
    }
    $pdf->Ln();

    // Table rows
    $pdf->SetFont('Arial', '', 10);
    foreach ($data as $row) {
        foreach ($columns as $key => $width) {
            $text = isset($row[$key]) ? $row[$key] : '';
            $align = is_numeric($text) ? 'C' : 'L'; // Center align numbers
            $pdf->Cell($width, 10, $text, 1, 0, $align);
        }
        $pdf->Ln();
    }

    $pdf->Output('D', 'inventory.pdf');
}

?>
