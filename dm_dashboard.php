<?php
session_start();

// Only accessible by DM role
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'dm') {
    header('HTTP/1.1 403 Forbidden');
    echo "Access denied. Please login as Decision Maker.";
    exit();
}

// include DB connection to show quick data
include __DIR__ . '/ahp/ahp_core.php';

$dm_name = htmlspecialchars($_SESSION['user_nama'] ?? 'Decision Maker');
$dm_number = htmlspecialchars($_SESSION['dm_number'] ?? '');
$dm_email = htmlspecialchars($_SESSION['user_email'] ?? '');
$dm_role_label = htmlspecialchars($_SESSION['dm_role_label'] ?? 'Decision Maker');

// Fetch available AHP input matrices
$matrices = [];
if (isset($conn) && $conn) {
    $q = $conn->query("SELECT id, level_name, last_updated FROM ahp_input_matrices ORDER BY level_name");
    if ($q) {
        while ($r = $q->fetch_assoc()) {
            $matrices[] = $r;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Decision Maker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(180deg,#f8f0ff 0%,#f0f7ff 100%); }
        .hero { background: linear-gradient(90deg,#a78bfa,#f0abfc); color:#fff; border-radius:12px; }
        .card-ghost { background: rgba(255,255,255,0.85); border: none; box-shadow: 0 6px 20px rgba(16,24,40,0.08); }
        .small-muted { color: #6b7280; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 hero d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1">Selamat datang, <?php echo $dm_name; ?></h2>
                    <div class="small-muted"><?php echo htmlspecialchars($dm_role_label); ?> (DM <?php echo $dm_number; ?>) — <span id="dmEmail"><?php echo $dm_email; ?></span></div>
                </div>
                <div class="text-end">
                    <a href="index.php" class="btn btn-light btn-sm me-2">Beranda</a>
                    <a href="ahp/ahp_result_summary.php" class="btn btn-outline-light btn-sm">Lihat Hasil AHP</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card card-ghost p-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div style="width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,#c4b5fd,#fbcfe8);display:flex;align-items:center;justify-content:center;font-weight:700;color:#4c1d95">DM</div>
                    </div>
                    <div>
                        <h5 class="mb-0"><?php echo $dm_name; ?></h5>
                        <small class="small-muted"><?php echo htmlspecialchars($dm_role_label); ?> — DM Number: <?php echo $dm_number; ?></small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary" onclick="copyEmail()">Salin Email</button>
                            <a href="logout.php" class="btn btn-sm btn-outline-secondary ms-2">Logout</a>
                        </div>
                    </div>
                </div>
                <hr>
                <p class="mb-0 small-muted">Dari dashboard ini Anda bisa meninjau matriks input AHP, melihat ringkasan hasil, dan mengunduh detail perhitungan.</p>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-ghost p-3">
                <h6 class="mb-3">AHP Matrices</h6>
                <?php if (empty($matrices)): ?>
                    <div class="text-center text-muted">Belum ada matriks AHP tersimpan.</div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($matrices as $m): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$m['level_name']))); ?></strong>
                                    <div class="small-muted">Terakhir diupdate: <?php echo htmlspecialchars($m['last_updated']); ?></div>
                                </div>
                                <div>
                                    <a href="ahp/ahp_weights.php?level=<?php echo urlencode($m['level_name']); ?>" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-ghost p-3">
                <h6>Notes</h6>
                <ul>
                    <li class="small-muted">Jika Anda perlu memasukkan preferensi, minta admin untuk memberikan akses input per-DM.</li>
                    <li class="small-muted">Semua perubahan DM dikelola melalui tabel <code>decision_makers</code> (admin hanya melihat, tidak mengubah sesuai GDSS ketentuan).</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function copyEmail(){
    const txt = document.getElementById('dmEmail').innerText;
    navigator.clipboard?.writeText(txt).then(()=>{ alert('Email disalin ke clipboard'); }).catch(()=>{ alert('Gagal menyalin'); });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
