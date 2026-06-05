<?php
require '../koneksi.php';
/** @var mysqli $koneksi */
$pageTitle = 'Tambah Pegawai';
$error = '';

// [SELECT] Ambil daftar departemen untuk dropdown (RELASI)
$deptList = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY nama ASC");

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

    // Validasi
    if (empty($nip) || empty($nama) || !$id_dept || empty($jabatan) || empty($tgl_masuk)) {
        $error = 'Field bertanda * wajib diisi!';
    } else {
        // Cek NIP duplikat
        $cek = mysqli_query($koneksi, "SELECT id FROM pegawai WHERE nip = '$nip'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'NIP sudah terdaftar, gunakan NIP lain!';
        } else {
            // [DML INSERT] Simpan pegawai baru
            $q = "INSERT INTO pegawai (nip, nama, id_departemen, jabatan, gaji, no_hp, email, tgl_masuk, status)
                  VALUES ('$nip','$nama',$id_dept,'$jabatan',$gaji,'$no_hp','$email','$tgl_masuk','$status')";
            if (mysqli_query($koneksi, $q)) {
                header("Location: index.php?pesan=tambah");
                exit;
            } else {
                $error = 'Gagal menyimpan: ' . mysqli_error($koneksi);
            }
        }
    }
}

require '../dashboard/navbar.php';
?>

<div class="container mt-4">
    <div class="mb-4">
        <a href="index.php" class="text-decoration-none text-muted small">← Kembali ke Daftar Pegawai</a>
        <h4 class="fw-bold mt-1">Tambah Pegawai Baru</h4>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger py-2">❌ <?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            📝 Form Data Pegawai
            <small class="d-block opacity-75 fw-normal" style="font-size:0.75rem;">
                INSERT INTO pegawai (...) VALUES (...)
            </small>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip"
                               value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>"
                               class="form-control font-monospace" placeholder="Contoh: 2024001">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                               class="form-control" placeholder="Nama lengkap pegawai">
                    </div>

                    <div class="col-md-6">
                        <!-- [RELASI] Dropdown isi dari tabel departemen -->
                        <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                        <select name="id_departemen" class="form-select">
                            <option value="">-- Pilih Departemen --</option>
                            <?php while ($d = mysqli_fetch_assoc($deptList)): ?>
                            <option value="<?= $d['id'] ?>"
                                <?= (isset($_POST['id_departemen']) && $_POST['id_departemen'] == $d['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nama']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan"
                               value="<?= htmlspecialchars($_POST['jabatan'] ?? '') ?>"
                               class="form-control" placeholder="Contoh: Staff IT">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="gaji"
                                   value="<?= htmlspecialchars($_POST['gaji'] ?? '') ?>"
                                   class="form-control" placeholder="Contoh: 5000000" min="0">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_masuk"
                               value="<?= htmlspecialchars($_POST['tgl_masuk'] ?? '') ?>"
                               class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" name="no_hp"
                               value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>"
                               class="form-control font-monospace" placeholder="081234567890">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               class="form-control" placeholder="nama@perusahaan.com">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif"    <?= (isset($_POST['status']) && $_POST['status']=='aktif')    ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= (isset($_POST['status']) && $_POST['status']=='nonaktif') ? 'selected' : '' ?>>Non-aktif</option>
                        </select>
                    </div>

                </div>

                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-semibold px-4">💾 Simpan Data</button>
                    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../dashboard/footer.php'; ?>
