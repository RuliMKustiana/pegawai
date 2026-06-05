<?php
require __DIR__ . '/../koneksi.php';
$pageTitle = 'Laporan';
require '../dashboard/navbar.php';

// [GROUP BY + SUM + AVG + MAX + MIN] Statistik gaji
$stat = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) as total,
            SUM(gaji)          as total_gaji,
            ROUND(AVG(gaji),0) as rata_gaji,
            MAX(gaji)          as tertinggi,
            MIN(gaji)          as terendah
     FROM pegawai WHERE status='aktif'"));

// [VIEW] Statistik per departemen
$per_dept = mysqli_query($koneksi,
    "SELECT * FROM v_statistik_dept ORDER BY total_gaji DESC");

// [ORDER BY DESC + LIMIT] Top 5 gaji tertinggi
$top_gaji = mysqli_query($koneksi,
    "SELECT p.nama, d.nama AS dept, p.jabatan, p.gaji
     FROM pegawai p
     JOIN departemen d ON p.id_departemen = d.id
     WHERE p.status = 'aktif'
     ORDER BY p.gaji DESC
     LIMIT 5");

// [GROUP BY] Jumlah pegawai per status
$per_status = mysqli_query($koneksi,
    "SELECT status, COUNT(*) as total
     FROM pegawai
     GROUP BY status");

// Log aktivitas (hasil TRIGGER)
$log       = mysqli_query($koneksi,
    "SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT 10");
$total_log = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) as t FROM log_aktivitas"))['t'];
?>

<div class="container mt-4">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">📈 Laporan & Statistik</h4>
        <p class="text-muted small mb-0">
            Menggunakan GROUP BY, SUM, AVG, MAX, MIN, VIEW, ORDER BY, LIMIT, dan TRIGGER
        </p>
    </div>

    <!-- Statistik gaji keseluruhan -->
    <div class="row g-3 mb-4">
        <?php
        $cards = [
            ['label' => 'Pegawai Aktif',   'val' => $stat['total'],                   'bg' => '#0d6efd'],
            ['label' => 'Total Gaji/Bulan','val' => rupiah($stat['total_gaji'] ?? 0),  'bg' => '#198754'],
            ['label' => 'Rata-rata Gaji',  'val' => rupiah($stat['rata_gaji']  ?? 0),  'bg' => '#6f42c1'],
            ['label' => 'Gaji Tertinggi',  'val' => rupiah($stat['tertinggi']  ?? 0),  'bg' => '#fd7e14'],
            ['label' => 'Gaji Terendah',   'val' => rupiah($stat['terendah']   ?? 0),  'bg' => '#dc3545'],
        ];
        foreach ($cards as $c):
        ?>
        <div class="col-6 col-md">
            <div class="stat-card" style="background:<?= $c['bg'] ?>;">
                <div class="small opacity-75 mb-1"><?= $c['label'] ?></div>
                <div class="fw-bold" style="font-size:1rem;"><?= $c['val'] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-4">

        <!-- Top 5 gaji tertinggi -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">
                    🏆 Top 5 Gaji Tertinggi
                    <small class="d-block text-muted fw-normal font-monospace" style="font-size:0.7rem;">
                        ORDER BY gaji DESC LIMIT 5
                    </small>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>Nama</th><th>Jabatan</th><th class="text-end">Gaji</th></tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; while ($r = mysqli_fetch_assoc($top_gaji)): ?>
                            <tr>
                                <td><?= $rank==1?'🥇':($rank==2?'🥈':($rank==3?'🥉':$rank)) ?></td>
                                <td>
                                    <div class="fw-semibold small"><?= htmlspecialchars($r['nama']) ?></div>
                                    <div class="text-muted" style="font-size:0.75rem;"><?= $r['dept'] ?></div>
                                </td>
                                <td class="small"><?= htmlspecialchars($r['jabatan']) ?></td>
                                <td class="text-end small fw-semibold"><?= rupiah($r['gaji']) ?></td>
                            </tr>
                            <?php $rank++; endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status pegawai [GROUP BY] -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">
                    📊 Pegawai per Status
                    <small class="d-block text-muted fw-normal font-monospace" style="font-size:0.7rem;">
                        SELECT status, COUNT(*) FROM pegawai GROUP BY status
                    </small>
                </div>
                <div class="card-body">
                    <?php while ($r = mysqli_fetch_assoc($per_status)): ?>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded mb-2"
                         style="background:#f8f9fa;">
                        <span class="badge badge-<?= $r['status'] ?> px-3 py-2 fs-6">
                            <?= ucfirst($r['status']) ?>
                        </span>
                        <span class="fw-bold fs-4"><?= $r['total'] ?> orang</span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Statistik per departemen (VIEW) -->
    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">
            🏛️ Ringkasan Gaji per Departemen
            <small class="d-block text-muted fw-normal font-monospace" style="font-size:0.7rem;">
                SELECT * FROM v_statistik_dept — VIEW: GROUP BY + SUM + AVG + MAX + MIN
            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Departemen</th>
                        <th class="text-center">Pegawai</th>
                        <th class="text-end">Total Gaji</th>
                        <th class="text-end">Rata-rata</th>
                        <th class="text-end">Tertinggi</th>
                        <th class="text-end">Terendah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = mysqli_fetch_assoc($per_dept)): ?>
                    <tr>
                        <td><code><?= $r['kode'] ?></code> <?= htmlspecialchars($r['nama']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary"><?= $r['total_pegawai'] ?></span>
                        </td>
                        <td class="text-end small fw-semibold"><?= rupiah($r['total_gaji']) ?></td>
                        <td class="text-end small text-success fw-semibold"><?= rupiah($r['rata_gaji']) ?></td>
                        <td class="text-end small"><?= rupiah($r['gaji_tertinggi']) ?></td>
                        <td class="text-end small"><?= rupiah($r['gaji_terendah']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log aktivitas (hasil TRIGGER) -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-semibold">🔔 Log Aktivitas</span>
                <small class="d-block text-muted fw-normal font-monospace" style="font-size:0.7rem;">
                    Diisi otomatis oleh TRIGGER saat pegawai dihapus atau status berubah
                </small>
            </div>
            <span class="badge bg-secondary"><?= $total_log ?> log</span>
        </div>

        <?php if ($total_log == 0): ?>
        <div class="card-body text-center py-5 text-muted">
            <div style="font-size:2.5rem;">📋</div>
            <p class="mt-2 mb-1">Belum ada log aktivitas</p>
            <small>Coba hapus data pegawai atau ubah status pegawai — TRIGGER akan otomatis mengisi tabel ini</small>
        </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
            <?php while ($r = mysqli_fetch_assoc($log)): ?>
            <div class="list-group-item d-flex align-items-start gap-3 py-3">
                <span class="badge bg-<?= $r['aksi']=='DELETE' ? 'danger' : 'warning text-dark' ?> mt-1">
                    <?= $r['aksi'] ?>
                </span>
                <div>
                    <p class="mb-0 small"><?= htmlspecialchars($r['keterangan']) ?></p>
                    <small class="text-muted font-monospace">
                        <?= date('d M Y, H:i:s', strtotime($r['waktu'])) ?>
                        — tabel: <?= $r['tabel'] ?>
                    </small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php require '../dashboard/footer.php'; ?>
