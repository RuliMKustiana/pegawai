<?php
require __DIR__ . '/../koneksi.php';
/** @var mysqli $koneksi */
$pageTitle = 'Departemen';
require '../dashboard/navbar.php';

// [VIEW] Statistik per departemen (GROUP BY + COUNT + SUM + AVG + MAX + MIN)
$result = mysqli_query($koneksi, "SELECT * FROM v_statistik_dept ORDER BY nama ASC");
$total  = mysqli_num_rows($result);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">🏛️ Departemen</h4>
            <p class="text-muted small mb-0"><?= $total ?> departemen terdaftar</p>
        </div>
        <a href="/departemen/tambah.php" class="btn btn-primary">+ Tambah Departemen</a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <small class="text-muted font-monospace">
                SELECT * FROM v_statistik_dept — VIEW: GROUP BY + COUNT, SUM, AVG, MAX, MIN
            </small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Departemen</th>
                        <th class="text-center">Pegawai Aktif</th>
                        <th class="text-end">Total Gaji</th>
                        <th class="text-end">Rata-rata Gaji</th>
                        <th class="text-end">Tertinggi</th>
                        <th class="text-end">Terendah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total == 0): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada departemen</td>
                    </tr>
                    <?php endif; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><code><?= $row['kode'] ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary"><?= $row['total_pegawai'] ?></span>
                        </td>
                        <td class="text-end small"><?= rupiah($row['total_gaji']) ?></td>
                        <td class="text-end small fw-semibold text-success"><?= rupiah($row['rata_gaji']) ?></td>
                        <td class="text-end small"><?= rupiah($row['gaji_tertinggi']) ?></td>
                        <td class="text-end small"><?= rupiah($row['gaji_terendah']) ?></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $row['id'] ?>"
                               class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= $row['id'] ?>"
                               onclick="return confirm('Hapus departemen ini?\nPastikan tidak ada pegawai di dalamnya!')"
                               class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../dashboard/footer.php'; ?>
