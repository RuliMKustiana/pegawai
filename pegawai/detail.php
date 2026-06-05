<?php
$koneksiPath = __DIR__ . '/../koneksi.php';
$loadedConnection = require $koneksiPath;
if ($loadedConnection !== 1) {
    $koneksi = $loadedConnection;
}
if (!isset($koneksi) && isset($GLOBALS['koneksi'])) {
    $koneksi = $GLOBALS['koneksi'];
}
if (!isset($koneksi) || !$koneksi) {
    die('Koneksi database tidak tersedia.');
}

$pageTitle = 'Detail Pegawai';

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// [VIEW] Ambil detail lengkap pegawai 
$pgw = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM v_pegawai 
WHERE id=$id"));
if (!$pgw) {
    header("Location: index.php");
    exit;
}

require __DIR__ . '/../dashboard/navbar.php';
?>

<div class="container mt-4">
    <div class="mb-4">
        <a href="../dashboard/index.php" class="text-decoration-none text-muted small">←
            Kembali ke Daftar</a>
        <h4 class="fw-bold mt-1">Detail Pegawai</h4>
    </div>

    <div class="row g-4">

        <!-- Kartu profil -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body py-4">
                    <!-- Avatar huruf pertama nama -->
                    <div style="width:80px; height:80px; border-radius:50%; 
                                background:#0d6efd; color:white; font-size:2rem; 
                                display:flex; align-items:center; justify-content:center; 
                                margin: 0 auto 1rem;">
                        <?= strtoupper(substr($pgw['nama'], 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($pgw['nama']) ?></h5>
                    7

                    <p class="text-muted small mb-2"><?= htmlspecialchars($pgw['jabatan'])
                                                        ?></p>
                    <span class="badge badge-<?= $pgw['status'] ?> px-3 py-2">
                        <?= ucfirst($pgw['status']) ?>
                    </span>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="../pegawai/edit.php?id=<?= $id ?>" class="btn btn-warning btn-sm 
flex-fill"> Edit</a>
                    <a href="../pegawai/hapus.php?id=<?= $id ?>"
                        onclick="return confirm('Yakin hapus pegawai ini?')"
                        class="btn btn-outline-danger btn-sm flex-fill">🗑 Hapus</a>
                </div>
            </div>
        </div>

        <!-- Informasi lengkap -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white fw-semibold">
                    Informasi Lengkap
                    <small class="text-muted d-block fw-normal" style="font-size:0.75rem;">
                        SELECT * FROM v_pegawai WHERE id = <?= $id ?>
                    </small>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <?php
                        $fields = [
                            ['NIP',           $pgw['nip'],                    true],
                            ['Departemen',    $pgw['departemen'],              false],
                            ['Jabatan',       $pgw['jabatan'],                 false],
                            ['Gaji Pokok',    rupiah($pgw['gaji']),            false],
                            ['No. HP',        $pgw['no_hp'] ?: '-',            true],
                            ['Email',         $pgw['email'] ?: '-',            false],
                            ['Tanggal Masuk', tglIndo($pgw['tgl_masuk']),      false],
                            ['Lama Bekerja',  $pgw['lama_kerja'] . ' tahun',   false],
                            ['Status',        ucfirst($pgw['status']),         false],
                        ];
                        foreach ($fields as [$label, $val, $mono]):
                        ?>
                            <tr>
                                <td class="text-muted small" style="width:35%"><?= $label ?></td>
                                <td class="fw-semibold <?= $mono ? 'font-monospace' : '' ?>">
                                    <?= htmlspecialchars($val) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                8

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../dashboard/footer.php'; ?>