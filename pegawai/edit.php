<?php
require '../koneksi.php';
/** @var mysqli $koneksi */

$pageTitle = 'Edit Pegawai';
$error = '';

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// [DML SELECT] Ambil data pegawai berdasarkan ID 
$pgw = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pegawai WHERE 
id=$id"));
if (!$pgw) {
    header("Location: index.php");
    exit;
}

// [SELECT] Daftar departemen untuk dropdown 
$deptList = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY nama 
ASC");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip       = trim($_POST['nip']);
    $nama      = trim($_POST['nama']);
    $id_dept   = (int)$_POST['id_departemen'];
    $jabatan   = trim($_POST['jabatan']);
    $gaji      = (float)$_POST['gaji'];
    $no_hp     = trim($_POST['no_hp']);
    $email     = trim($_POST['email']);
    $tgl_masuk = trim($_POST['tgl_masuk']);
    $status    = trim($_POST['status']);
    if (empty($nip) || empty($nama) || !$id_dept || empty($jabatan)) {
        $error = 'Field bertanda * wajib diisi!';
    } else {
        $cek = mysqli_query($koneksi, "SELECT id FROM pegawai WHERE nip='$nip' AND 
id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'NIP sudah digunakan pegawai lain!';
        } else {
            // [DML UPDATE] — TRIGGER otomatis jalan jika status berubah 
            $q = "UPDATE pegawai SET 
                    nip='$nip', nama='$nama', id_departemen=$id_dept, 
                    jabatan='$jabatan', gaji=$gaji, no_hp='$no_hp', 
                    email='$email', tgl_masuk='$tgl_masuk', status='$status' 
                  WHERE id = $id";
            if (mysqli_query($koneksi, $q)) {
                header("Location: index.php?pesan=edit");
                exit;
            } else {
                $error = 'Gagal: ' . mysqli_error($koneksi);
            }
        }
    }
    $pgw = array_merge($pgw, $_POST);
}

require '../dashboard/navbar.php';
?>

<div class="container mt-4">
    <div class="mb-4">
        <a href="index.php" class="text-decoration-none text-muted small">← Kembali</a>
        <h4 class="fw-bold mt-1">Edit Data Pegawai</h4>
    </div>

    <div class="alert alert-warning py-2 small">
        Mengedit: <strong><?= htmlspecialchars($pgw['nama']) ?></strong>
        (NIP: <code><?= $pgw['nip'] ?></code>)
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"> <?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-warning">
            Form Edit Pegawai
            <small class="d-block fw-normal" style="font-size:0.75rem;">
                UPDATE pegawai SET ... WHERE id = <?= $id ?>
            </small>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIP <span class="text
danger">*</span></label>
                        <input type="text" name="nip"
                            value="<?= htmlspecialchars($pgw['nip']) ?>"
                            class="form-control font-monospace">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama <span class="text
danger">*</span></label>
                        <input type="text" name="nama"
                            value="<?= htmlspecialchars($pgw['nama']) ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Departemen <span class="text
danger">*</span></label>
                        <select name="id_departemen" class="form-select">
                            <?php while ($d = mysqli_fetch_assoc($deptList)): ?>
                                <option value="<?= $d['id'] ?>"
                                    <?= $pgw['id_departemen'] == $d['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    4


                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jabatan <span class="text
danger">*</span></label>
                        <input type="text" name="jabatan"
                            value="<?= htmlspecialchars($pgw['jabatan']) ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gaji</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="gaji"
                                value="<?= $pgw['gaji'] ?>"
                                class="form-control" min="0">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                        <input type="date" name="tgl_masuk"
                            value="<?= $pgw['tgl_masuk'] ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" name="no_hp"
                            value="<?= htmlspecialchars($pgw['no_hp'] ?? '') ?>"
                            class="form-control font-monospace">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($pgw['email'] ?? '') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif" <?= $pgw['status'] == 'aktif'    ? 'selected' : ''
                                                    ?>>Aktif</option>
                            <option value="nonaktif" <?= $pgw['status'] == 'nonaktif' ? 'selected' : ''
                                                        ?>>Non-aktif</option>
                        </select>
                        <small class="text-muted">
                            Perubahan status dicatat otomatis oleh TRIGGER
                            5

                        </small>
                    </div>

                </div>

                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning fw-semibold px-4"> Simpan
                        Perubahan</button>
                    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../dashboard/footer.php'; ?>