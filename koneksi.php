<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "db_pegawai";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("
    <div class='container mt-5'>
        <div class='alert alert-danger' role='alert'>
            Koneksi gagal: " . mysqli_connect_error() . "
        </div>
    </div>");
}
mysqli_set_charset($koneksi, "utf8");

function rupiah($angka)
{
    $hasil_rupiah = "Rp" . number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
} 

function tglIndo($tgl)
{
    if (!$tgl) return '-';
    $bulan = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $d = explode('-', $tgl);
    return $d[2] . ' ' . $bulan[(int)$d[1]] . ' ' . $d[0];
}
?>
