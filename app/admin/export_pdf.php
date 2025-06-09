<?php
require('fpdf/fpdf.php');
include '../koneksi.php';

$bank_id = isset($_GET['bank_id']) ? intval($_GET['bank_id']) : 0;
$bank = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT bank_nama FROM bank WHERE bank_id=$bank_id"));

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Laporan Transaksi - Bank ' . $bank['bank_nama'], 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, 'No', 1);
$pdf->Cell(30, 8, 'Tanggal', 1);
$pdf->Cell(50, 8, 'Kategori', 1);
$pdf->Cell(40, 8, 'Pemasukan', 1);
$pdf->Cell(40, 8, 'Pengeluaran', 1);
$pdf->Ln();

$data = mysqli_query($koneksi, "SELECT t.*, k.kategori FROM transaksi t JOIN kategori k ON k.kategori_id = t.transaksi_kategori WHERE transaksi_bank = $bank_id ORDER BY transaksi_id DESC");

$no = 1;
$pdf->SetFont('Arial', '', 10);
while ($row = mysqli_fetch_assoc($data)) {
    $pdf->Cell(10, 8, $no++, 1);
    $pdf->Cell(30, 8, date('d-m-Y', strtotime($row['transaksi_tanggal'])), 1);
    $pdf->Cell(50, 8, $row['kategori'], 1);
    $pdf->Cell(40, 8, ($row['transaksi_jenis'] == 'Pemasukan' ? 'Rp. ' . number_format($row['transaksi_nominal']) : '-'), 1);
    $pdf->Cell(40, 8, ($row['transaksi_jenis'] == 'Pengeluaran' ? 'Rp. ' . number_format($row['transaksi_nominal']) : '-'), 1);
    $pdf->Ln();
}

$pdf->Output();
?>

