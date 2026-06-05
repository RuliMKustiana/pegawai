<?php
require '../koneksi.php';
/** @var mysqli $koneksi */

$pageTitle = 'Tambah Departemen';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = strtoupper(trim($_POST['kode']));
    $nama = trim($_POST['nama']);
    if (empty($kode) || empty($nama)) {
        $error = 'Semua field wajib diisi!';
    } else {
        $cek = mysqli_query($koneksi, "SELECT id FROM departemen WHERE kode='$kode'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Kode departemen sudah digunakan!';
        } else {
            // [DML INSERT]
            mysqli_query($koneksi, "INSERT INTO departemen (kode, nama) VALUES ('$kode','$nama')");
            header("Location: index.php?pesan=tambah");
            exit;
        }
    }
}
require '../dashboard/navbar.php';
?>
<div class="container mt-4" style="max-width:500px">
    <div class="mb-4">
        <a href="index.php" class="text-muted small text-decoration-none">← Kembali</a>
        <h4 class="fw-bold mt-1">Tambah Departemen</h4>
    </div>
    <?php if ($error): ?>
    <div class="alert alert-danger py-2 small">❌ <?= $error ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-header bg-primary text-white">
            📝 Form Departemen
            <small class="d-block fw-normal opacity-75" style="font-size:0.75rem;">
                INSERT INTO departemen (kode, nama) VALUES (...)
            </small>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                    <input type="text" name="kode"
                           value="<?= htmlspecialchars($_POST['kode'] ?? '') ?>"
                           class="form-control font-monospace text-uppercase"
                           placeholder="Contoh: IT" maxlength="10">
                    <small class="text-muted">Maksimal 10 karakter, otomatis huruf besar</small>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Nama Departemen <span class="text-danger">*</span></label>
                    <input type="text" name="nama"
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                           class="form-control" placeholder="Contoh: Teknologi Informasi">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-semibold px-4">💾 Simpan</button>
                    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require '../dashboard/footer.php'; ?>
