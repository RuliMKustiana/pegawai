<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistem Pegawai' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .navbar-brand { font-weight: 700; }
        .nav-link.active { background: rgba(255,255,255,0.2); border-radius: 6px; }
        .card { border: none; box-shadow: 0 1px 6px rgba(0,0,0,0.07); border-radius: 10px; }
        .card-header { border-radius: 10px 10px 0 0 !important; }
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-aktif { background: #d1fae5; color: #065f46; }
        .badge-nonaktif { background: #fee2e2; color: #991b1b; }
        .stat-card { border-radius: 10px; padding: 1.25rem; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <?php 
        
        $hitungFolder = substr_count($_SERVER['PHP_SELF'], '/') - 2;
        $base = str_repeat('../', max(0, $hitungFolder));
        $current = $_SERVER['PHP_SELF'];

        $menus = [
            ['url' => 'dashboard/index.php',  'folder' => 'dashboard', 'label' => '📊 Dashboard'],
            ['url' => 'pegawai/index.php',    'folder' => 'pegawai',   'label' => '👤 Pegawai'],
            
        ];
        ?>

        <a class="navbar-brand" href="<?= $base ?>dashboard/index.php">
            🏢 Sistem Pegawai
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <?php foreach ($menus as $m): 
                    $active = strpos($current, '/' . $m['folder'] . '/') !== false; 
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= $base . $m['url'] ?>">
                            <?= $m['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if (isset($_GET['pesan'])): ?>
    <?php 
    $pesanMap = [
        'tambah' => ['teks' => 'Data berhasil ditambahkan!', 'tipe' => 'success'],
        'edit'   => ['teks' => 'Data berhasil diubah!', 'tipe' => 'info'],
        'hapus'  => ['teks' => 'Data berhasil dihapus!', 'tipe' => 'warning'],
    ];
    $p = $pesanMap[$_GET['pesan']] ?? null;
    ?>
    <?php if ($p): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $p['tipe'] ?> alert-dismissible fade show py-2" role="alert">
                <?= $p['tipe'] == 'success' ? '✅' : ($p['tipe'] == 'warning' ? '🗑️' : '✏️') ?> 
                <?= $p['teks'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>