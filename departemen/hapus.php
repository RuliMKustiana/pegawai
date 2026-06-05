<?php
require __DIR__ . '/../koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek apakah masih ada pegawai (FOREIGN KEY RESTRICT)
$cek = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) as t FROM pegawai WHERE id_departemen=$id"))['t'];

if ($cek > 0) {
    // Tidak bisa hapus — masih ada pegawai
    header("Location: index.php?pesan=gagal");
} else {
    // [DML DELETE]
    mysqli_query($koneksi, "DELETE FROM departemen WHERE id=$id");
    header("Location: index.php?pesan=hapus");
}
exit;
?>
