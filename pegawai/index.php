<?php 
require '../koneksi.php'; 
/** @var mysqli $koneksi */ 
$pageTitle = "Data Pegawai"; 
require '../dashboard/navbar.php'; 

if (!function_exists('rupiah')) { 
    function rupiah($angka) { 
        return "Rp " . number_format($angka, 0, ',', '.'); 
    } 
} 

if (!function_exists('tglIndo')) { 
    function tglIndo($tgl) { 
        return date('d-m-Y', strtotime($tgl)); 
    } 
} 

// 1. PERBAIKAN: Gunakan $currentPage agar tidak tertimpa
$currentPage = isset($_GET['hal']) ? (int)$_GET['hal'] : 1; 
if ($currentPage < 1) { $currentPage = 1; }

$perPage = 9; 
$offset = ($currentPage - 1) * $perPage; 
$keyword = isset($_GET['cari']) ? $_GET['cari'] : ''; 

if ($keyword) { 
    $result = mysqli_query($koneksi, "SELECT * FROM v_pegawai WHERE nama LIKE '%$keyword%' OR nip LIKE '%$keyword%' OR jabatan LIKE '%$keyword%' ORDER BY nama ASC LIMIT $perPage OFFSET $offset"); 
    $totalRow = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM v_pegawai WHERE nama LIKE '%$keyword%' OR nip LIKE '%$keyword%' OR jabatan LIKE '%$keyword%'"))['total']; 
} else { 
    $result = mysqli_query($koneksi, "SELECT * FROM v_pegawai ORDER BY nama ASC LIMIT $perPage OFFSET $offset"); 
    $totalRow = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM v_pegawai"))['total']; 
} 

// 2. PERBAIKAN: Simpan ke variabel $totalPages yang berbeda
$totalPages = ceil($totalRow / $perPage); 
if ($totalPages < 1) { $totalPages = 1; }
?> 

<div class="container mt-4"> 
    <div class="d-flex justify-content-between align-items-center mb-4"> 
        <div> 
            <h4 class="fw-bold mb-1">👤 Data Pegawai</h4> 
            <p class="text-muted small mb-0"><?= $totalRow ?> pegawai ditemukan</p> 
        </div> 
        <a href="tambah.php" class="btn btn-primary fw-semibold">+ Tambah Pegawai</a> 
    </div> 

    <!-- Pencarian --> 
    <div class="card mb-4"> 
        <div class="card-body py-3"> 
            <form method="GET" class="d-flex gap-2"> 
                <input type="text" name="cari" value="<?= htmlspecialchars($keyword) ?>" class="form-control" placeholder="🔍 Cari nama, NIP, atau jabatan..."> 
                <button type="submit" class="btn btn-outline-primary">Cari</button> 
                <?php if ($keyword): ?> 
                    <a href="index.php" class="btn btn-outline-secondary">Reset</a> 
                <?php endif; ?> 
            </form> 
            <?php if ($keyword): ?> 
                <small class="text-muted mt-1 d-block"> Hasil: <strong>"<?= htmlspecialchars($keyword) ?>"</strong> &mdash; <span class="font-monospace">WHERE nama LIKE '%<?= htmlspecialchars($keyword) ?>%'</span> </small> 
            <?php endif; ?> 
        </div> 
    </div> 

    <!-- Tabel --> 
    <div class="card"> 
        <div class="card-header bg-white"> 
            <small class="text-muted font-monospace"> SELECT * FROM v_pegawai ORDER BY nama ASC LIMIT <?= $perPage ?> OFFSET <?= $offset ?> </small> 
        </div> 
        <div class="card-body p-0"> 
            <div class="table-responsive"> 
                <table class="table table-hover mb-0"> 
                    <thead class="table-primary"> 
                        <tr> 
                            <th>NIP</th> 
                            <th>Nama & Jabatan</th> 
                            <th>Departemen</th> 
                            <th>Gaji</th> 
                            <th>Tgl Masuk</th> 
                            <th>Status</th> 
                            <th class="text-center">Aksi</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php if ($totalRow == 0): ?> 
                            <tr> 
                                <td colspan="7" class="text-center py-5 text-muted"> Tidak ada data pegawai </td> 
                            </tr> 
                        <?php endif; ?> 
                        <?php while ($row = mysqli_fetch_assoc($result)): ?> 
                            <tr> 
                                <td><code class="small"><?= $row['nip'] ?></code></td> 
                                <td> 
                                    <div class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></div> 
                                    <div class="text-muted small"><?= htmlspecialchars($row['jabatan']) ?></div> 
                                </td> 
                                <td> <span class="badge bg-light text-dark border"> <?= htmlspecialchars($row['departemen']) ?> </span> </td> 
                                <td class="small fw-semibold"><?= rupiah($row['gaji']) ?></td> 
                                <td class="small"><?= tglIndo($row['tgl_masuk']) ?></td> 
                                <td> <span class="badge bg-<?= $row['status'] == 'aktif' ? 'success' : 'danger' ?> px-2 py-1"> <?= ucfirst($row['status']) ?> </span> </td> 
                                <td class="text-center"> 
                                    <a href="../pegawai/detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm">Detail</a> 
                                    <a href="../pegawai/edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a> 
                                    <a href="../pegawai/hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus pegawai <?= htmlspecialchars($row['nama']) ?>?')" class="btn btn-danger btn-sm">Hapus</a> 
                                </td> 
                            </tr> 
                        <?php endwhile; ?> 
                    </tbody> 
                </table> 
            </div> 
        </div> 

        <!-- 3. PERBAIKAN: Logika Tampilan Blok HTML Pagination --> 
        <?php if ($totalPages > 1): ?> 
            <div class="card-footer bg-white d-flex justify-content-between align-items-center"> 
                <small class="text-muted">Halaman <?= $currentPage ?> dari <?= $totalPages ?></small> 
                <nav> 
                    <ul class="pagination pagination-sm mb-0"> 
                        <!-- Tombol Previous -->
                        <?php if ($currentPage > 1): ?> 
                            <li class="page-item"> 
                                <a class="page-link" href="?hal=<?= $currentPage - 1 ?>&cari=<?= urlencode($keyword) ?>">‹</a> 
                            </li> 
                        <?php endif; ?> 

                        <!-- Angka Halaman -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?> 
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>"> 
                                <a class="page-link" href="?hal=<?= $i ?>&cari=<?= urlencode($keyword) ?>"><?= $i ?></a> 
                            </li> 
                        <?php endfor; ?> 

                        <!-- Tombol Next -->
                        <?php if ($currentPage < $totalPages): ?> 
                            <li class="page-item"> 
                                <a class="page-link" href="?hal=<?= $currentPage + 1 ?>&cari=<?= urlencode($keyword) ?>">›</a> 
                            </li> 
                        <?php endif; ?> 
                    </ul> 
                </nav> 
            </div> 
        <?php endif; ?> 
    </div> 
</div> 

<?php require '../dashboard/footer.php'; ?>
