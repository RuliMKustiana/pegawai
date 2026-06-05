<?php
require __DIR__ . '/../koneksi.php';
$id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dept  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM departemen WHERE id=$id"));
if (!$dept) { header("Location: index.php"); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = strtoupper(trim($_POST['kode']));
    $nama = trim($_POST['nama']);
    if (empty($kode) || empty($nama)) {
        $error = 'Semua field wajib diisi!';
    } else {
        $cek = mysqli_query($koneksi, "SELECT id FROM departemen WHERE kode='$kode' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Kode sudah digunakan departemen lain!';
        } else {
            // [DML UPDATE]
            mysqli_query($koneksi, "UPDATE departemen SET kode='$kode', nama='$nama' WHERE id=$id");
            header("Location: index.php?pesan=edit");
            exit;
        }
    }
    $dept = array_merge($dept, $_POST);
}
$pageTitle = 'Edit Departemen';
require '../dashboard/navbar.php';
?>
<div class="container mt-4" style="max-width:500px">
    <div class="mb-4">
        <a href="index.php" class="text-muted small text-decoration-none">← Kembali</a>
        <h4 class="fw-bold mt-1">Edit Departemen</h4>
    </div>
    <?php if ($error): ?>
    <div class="alert alert-danger py-2 small">❌ <?= $error ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-header bg-warning">
            📝 Edit Departemen
            <small class="d-block fw-normal" style="font-size:0.75rem;">
                UPDATE departemen SET ... WHERE id = <?= $id ?>
            </small>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                    <input type="text" name="kode"
                           value="<?= htmlspecialchars($dept['kode']) ?>"
                           class="form-control font-monospace text-uppercase" maxlength="10">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="nama"
                           value="<?= htmlspecialchars($dept['nama']) ?>"
                           class="form-control">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning fw-semibold px-4">💾 Simpan</button>
                    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require '../dashboard/footer.php'; ?>
