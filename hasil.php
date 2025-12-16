<?php
// FILE: hasil.php
session_start();

// Load Dependencies
require_once 'ahp/ahp_weights.php'; // File yang baru saja kita update
require_once 'includes/topsis.php';
require_once 'includes/helper.php';

// CEK: Apakah Admin sudah mengisi bobot?
if (empty($ahp_global) || empty($ahp_minat_bakat)) {
    echo "<div style='padding:50px; text-align:center; font-family:sans-serif;'>";
    echo "<h2 style='color:red;'>⚠️ Sistem Belum Siap</h2>";
    echo "<p>Admin/Pakar belum memasukkan bobot kriteria (GDSS).</p>";
    echo "<p>Harap hubungi administrator untuk melakukan input data pakar terlebih dahulu.</p>";
    echo "<a href='index.php'>Kembali</a>";
    echo "</div>";
    exit;
}

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: input.php');
    exit;
}

// =========================================================================
// 1. AMBIL INPUT PENGGUNA (36 Pertanyaan)
// =========================================================================
$userInput = [];
// Daftar field sesuai input.php
$fields = [
    'minat_mv_1', 'minat_mv_2', 'minat_mv_3', 'minat_mv_4',
    'minat_prog_1', 'minat_prog_2', 'minat_prog_3', 'minat_prog_4',
    'minat_sc_1', 'minat_sc_2', 'minat_sc_3', 'minat_sc_4',
    'skripsi_mv_1', 'skripsi_mv_2', 'skripsi_mv_3', 'skripsi_mv_4',
    'skripsi_prog_1', 'skripsi_prog_2', 'skripsi_prog_3', 'skripsi_prog_4',
    'skripsi_sc_1', 'skripsi_sc_2', 'skripsi_sc_3', 'skripsi_sc_4',
    'pekerjaan_mv_1', 'pekerjaan_mv_2', 'pekerjaan_mv_3', 'pekerjaan_mv_4',
    'pekerjaan_prog_1', 'pekerjaan_prog_2', 'pekerjaan_prog_3', 'pekerjaan_prog_4',
    'pekerjaan_sc_1', 'pekerjaan_sc_2', 'pekerjaan_sc_3', 'pekerjaan_sc_4'
];

foreach ($fields as $field) {
    // Default nilai 3 (Netral) jika error, agar tidak fatal error
    $val = isset($_POST[$field]) ? (int)$_POST[$field] : 3;
    $userInput[$field] = $val;
}

// =========================================================================
// 2. HITUNG TOPSIS MENGGUNAKAN BOBOT PAKAR (GDSS)
// =========================================================================
// Variabel $ahp_... ini berasal dari include 'ahp/ahp_weights.php'
$ahpWeights = [
    'global'       => $ahp_global,
    'minat_bakat'  => $ahp_minat_bakat,
    'tema_skripsi' => $ahp_tema_skripsi,
    'pekerjaan'    => $ahp_pekerjaan,
];

// Proses TOPSIS
// (Pastikan fungsi processFullTOPSIS di includes/topsis.php sudah support array ini)
$results = processFullTOPSIS($userInput, $ahpWeights);

// Simpan hasil ke Session untuk ditampilkan di detail.php
$_SESSION['results'] = $results;
$_SESSION['ahp_detail'] = $ahp_detail; // Detail perhitungan AHP Admin

// Data Tampilan
$labels = ['MV' => 'Mathematics & Visualization', 'Pemprog' => 'Pemrograman', 'SC' => 'Sistem Cerdas'];
$emojis = ['MV' => '🧮', 'Pemprog' => '💻', 'SC' => '💡'];
$colors = ['MV' => 'primary', 'Pemprog' => 'success', 'SC' => 'warning'];

$winner = $results['global']['best'] ?? 'MV';
$winnerScore = $results['global']['best_score'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi ✨</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .winner-card { background: linear-gradient(135deg, #6a05ad, #8a2be2); color: white; border: none; }
        .score-display { font-size: 3.5rem; font-weight: 800; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="color: #4b0082;">Rekomendasi Peminatan 🎯</h1>
            <p class="text-muted">Berdasarkan konsensus pakar dan jawaban Anda</p>
        </div>

        <div class="card winner-card shadow-lg mb-5 text-center">
            <div class="card-body py-5">
                <div class="display-1 mb-3"><?= $emojis[$winner] ?></div>
                <h2 class="fw-bold text-uppercase"><?= $labels[$winner] ?></h2>
                <div class="score-display"><?= number_format($winnerScore, 4) ?></div>
                <p class="fs-5 mt-2">Skor Preferensi Tertinggi</p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="fw-bold mb-4 text-center" style="color:#4b0082">Perbandingan Skor Akhir</h4>
                <?php foreach ($results['global']['ranked'] as $alt => $score): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong><?= $emojis[$alt] ?> <?= $labels[$alt] ?></strong>
                        <span><?= number_format($score, 4) ?></span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-<?= $colors[$alt] ?>" style="width: <?= $score * 100 ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="detail.php" class="btn btn-outline-primary rounded-pill px-4">📊 Lihat Detail Perhitungan (Transparansi)</a>
            <a href="input.php" class="btn btn-primary rounded-pill px-4 ms-2">🔄 Tes Ulang</a>
        </div>
    </div>
</body>
</html>