<?php 
require '../koneksi.php'; 
/** @var mysqli $koneksi */ 
 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
// [DML DELETE] — TRIGGER tr_hapus_pegawai otomatis catat ke log_aktivitas 
mysqli_query($koneksi, "DELETE FROM pegawai WHERE id = $id"); 
header("Location: index.php?pesan=hapus"); 
exit; 
?> 