<?php
require '../koneksi.php';
/** @var mysqli $koneksi */

$pageTitle = 'Dashboard — Sistem Pegawai';
require 'navbar.php';


$total_pegawai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM pegawai"))['t'];
$total_aktif   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM pegawai WHERE status='aktif'"))['t'];
$total_dept    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as t FROM departemen"))['t'];


$total_gaji    = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT SUM(gaji) as t FROM pegawai WHERE status='aktif'"))['t'];


$pegawai_baru  = mysqli_query($koneksi,
    "SELECT * FROM v_pegawai ORDER BY id DESC LIMIT 5");


$per_dept      = mysqli_query($koneksi,
    "SELECT * FROM v_statistik_dept ORDER BY total_pegawai DESC");
?>

<div class="container mt-4">

    
    <div class="mb-4">
        <h4 class="fw-bold mb-1">📊 Dashboard</h4>
        <p class="text-muted small mb-0">Ringkasan data sistem pendataan pegawai</p>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:#0d6efd;">
                <div style="font-size:2rem;">👥</div>
                <div style="font-size:1.8rem; font-weight:700;"><?= $total_pegawai ?></div>
                <div class="small opacity-75">Total Pegawai</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:#198754;">
                <div style="font-size:2rem;">✅</div>
                <div style="font-size:1.8rem; font-weight:700;"><?= $total_aktif ?></div>
                <div class="small opacity-75">Pegawai Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:#6f42c1;">
                <div style="font-size:2rem;">🏛️</div>
                <div style="font-size:1.8rem; font-weight:700;"><?= $total_dept ?></div>
                <div class="small opacity-75">Departemen</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="background:#fd7e14;">
                <div style="font-size:2rem;">💰</div>
                <div style="font-size:1.1rem; font-weight:700;"><?= rupiah($total_gaji ?? 0) ?></div>
                <div class="small opacity-75">Total Gaji/Bulan</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">
                    🏛️ Pegawai per Departemen
                    <small class="text-muted d-block fw-normal" style="font-size:0.75rem;">
                        SELECT nama, COUNT(*) FROM v_statistik_dept GROUP BY nama
                    </small>
                </div>
                <div class="card-body">
                    <?php
                    $rows = [];
                    $maxTotal = 1;
                    while ($r = mysqli_fetch_assoc($per_dept)) {
                        $rows[] = $r;
                        if ($r['total_pegawai'] > $maxTotal) $maxTotal = $r['total_pegawai'];
                    }
                    foreach ($rows as $r):
                        $pct = round(($r['total_pegawai'] / $maxTotal) * 100);
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold"><?= htmlspecialchars($r['nama']) ?></span>
                            <span class="small text-muted"><?= $r['total_pegawai'] ?> orang</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

       
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold">👤 Pegawai Terbaru</span>
                        <small class="text-muted d-block fw-normal" style="font-size:0.75rem;">
                            SELECT * FROM v_pegawai ORDER BY id DESC LIMIT 5
                        </small>
                    </div>
                    <a href="../pegawai/index.php" class="btn btn-outline-primary btn-sm">Lihat semua</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = mysqli_fetch_assoc($pegawai_baru)): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= htmlspecialchars($r['nama']) ?></div>
                                    <div class="text-muted" style="font-size:0.75rem;"><?= $r['jabatan'] ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $r['kode_dept'] ?></span></td>
                                <td>
                                    <span class="badge badge-<?= $r['status'] ?> px-2 py-1">
                                        <?= ucfirst($r['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require 'footer.php'; ?>
