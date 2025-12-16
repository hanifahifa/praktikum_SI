<?php
session_start();

// Cek apakah ada hasil di session, jika tidak ada, arahkan ke halaman input
if (!isset($_SESSION['results']) || !isset($_SESSION['ahp_detail'])) {
    header('Location: input.php');
    exit;
}

$results = $_SESSION['results'];
$ahp_detail = $_SESSION['ahp_detail'];

// Sesuaikan path jika helper.php berada di folder 'includes'
require_once 'includes/helper.php';

// Label alternatif untuk referensi (tidak digunakan dalam perhitungan)
$labels = [
    'MV' => 'Mathematics & Visualization',
    'Pemprog' => 'Pemrograman',
    'SC' => 'Supply Chain'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Perhitungan 📊</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .detail-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(147, 51, 234, 0.08);
        }
        .table-custom {
            font-size: 0.9rem;
        }
        .table-custom th {
            background: linear-gradient(135deg, #9333EA, #A855F7);
            color: white;
            font-weight: 600;
            text-align: center;
        }
        .table-custom td {
            text-align: center;
        }
        .badge-cr-pass {
            background: #10B981;
            color: white;
        }
        .badge-cr-fail {
            background: #EF4444;
            color: white;
        }
        .sticky-top-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="sticky-top-nav">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">📊 Detail Perhitungan</h5>
                <div>
                    <a href="hasil.php" class="btn btn-sm btn-outline-primary rounded-pill me-2">← Kembali</a>
                    <a href="input.php" class="btn btn-sm btn-primary rounded-pill">Tes Ulang</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="badge bg-gradient-purple mb-3">Transparansi Perhitungan</span>
            <h1 class="display-5 fw-bold gradient-text">Detail Lengkap AHP–TOPSIS</h1>
            <p class="text-muted">Semua langkah perhitungan ditampilkan secara transparan</p>
        </div>

        <div class="detail-section">
            <h2 class="fw-bold mb-4">🔢 Analytical Hierarchy Process (AHP)</h2>
            <p class="text-muted mb-4">Bobot kriteria ditentukan menggunakan metode AHP berdasarkan perbandingan berpasangan.</p>

            <h4 class="fw-bold mt-4 mb-3">1️⃣ Bobot Kriteria Minat & Bakat</h4>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Bobot</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ahp_detail['minat_bakat']['weights'] ?? [] as $criteria => $weight): ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($criteria) ?></td>
                            <td><?= formatNumber($weight) ?></td>
                            <td><span class="badge bg-primary"><?= round($weight * 100, 2) ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info">
                <strong>Consistency Ratio (CR):</strong> <?= formatNumber($ahp_detail['minat_bakat']['CR'] ?? 0) ?>
                <span class="badge <?= ($ahp_detail['minat_bakat']['CR'] ?? 0) < 0.1 ? 'badge-cr-pass' : 'badge-cr-fail' ?> ms-2">
                    <?= ($ahp_detail['minat_bakat']['CR'] ?? 0) < 0.1 ? '✓ Konsisten' : '✗ Tidak Konsisten' ?>
                </span>
            </div>

            <h4 class="fw-bold mt-5 mb-3">2️⃣ Bobot Kriteria Tema Skripsi</h4>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Bobot</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ahp_detail['tema_skripsi']['weights'] ?? [] as $criteria => $weight): ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($criteria) ?></td>
                            <td><?= formatNumber($weight) ?></td>
                            <td><span class="badge bg-success"><?= round($weight * 100, 2) ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info">
                <strong>Consistency Ratio (CR):</strong> <?= formatNumber($ahp_detail['tema_skripsi']['CR'] ?? 0) ?>
                <span class="badge <?= ($ahp_detail['tema_skripsi']['CR'] ?? 0) < 0.1 ? 'badge-cr-pass' : 'badge-cr-fail' ?> ms-2">
                    <?= ($ahp_detail['tema_skripsi']['CR'] ?? 0) < 0.1 ? '✓ Konsisten' : '✗ Tidak Konsisten' ?>
                </span>
            </div>

            <h4 class="fw-bold mt-5 mb-3">3️⃣ Bobot Kriteria Pekerjaan</h4>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Bobot</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ahp_detail['pekerjaan']['weights'] ?? [] as $criteria => $weight): ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($criteria) ?></td>
                            <td><?= formatNumber($weight) ?></td>
                            <td><span class="badge bg-warning"><?= round($weight * 100, 2) ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info">
                <strong>Consistency Ratio (CR):</strong> <?= formatNumber($ahp_detail['pekerjaan']['CR'] ?? 0) ?>
                <span class="badge <?= ($ahp_detail['pekerjaan']['CR'] ?? 0) < 0.1 ? 'badge-cr-pass' : 'badge-cr-fail' ?> ms-2">
                    <?= ($ahp_detail['pekerjaan']['CR'] ?? 0) < 0.1 ? '✓ Konsisten' : '✗ Tidak Konsisten' ?>
                </span>
            </div>

            <h4 class="fw-bold mt-5 mb-3">4️⃣ Bobot Global (Antar Kategori)</h4>
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Bobot</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ahp_detail['global']['weights'] ?? [] as $category => $weight): ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($category) ?></td>
                            <td><?= formatNumber($weight) ?></td>
                            <td><span class="badge bg-danger"><?= round($weight * 100, 2) ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info">
                <strong>Consistency Ratio (CR):</strong> <?= formatNumber($ahp_detail['global']['CR'] ?? 0) ?>
                <span class="badge <?= ($ahp_detail['global']['CR'] ?? 0) < 0.1 ? 'badge-cr-pass' : 'badge-cr-fail' ?> ms-2">
                    <?= ($ahp_detail['global']['CR'] ?? 0) < 0.1 ? '✓ Konsisten' : '✗ Tidak Konsisten' ?>
                </span>
            </div>
        </div>

        <?php
        /**
         * Fungsi untuk menampilkan detail langkah TOPSIS
         * @param string $title Judul bagian
         * @param string $emoji Emoji dekoratif
         * @param array $data Data hasil TOPSIS
         * @param array $criteriaNames Nama-nama kriteria
         * @param string $color Warna badge untuk skor
         */
        function displayTOPSISDetail($title, $emoji, $data, $criteriaNames, $color = 'primary') {
            // Label alternatif didefinisikan secara lokal atau di luar fungsi jika diperlukan.
            $alternatives = ['MV', 'Pemprog', 'SC'];
            ?>
            <div class="detail-section">
                <h2 class="fw-bold mb-4"><?= $emoji ?> <?= $title ?></h2>

                <h5 class="fw-bold mt-4 mb-3">1. Matriks Keputusan</h5>
                <div class="table-responsive">
                    <table class="table table-custom table-bordered">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                <?php foreach ($criteriaNames as $crit): ?>
                                <th><?= htmlspecialchars($crit) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // PERBAIKAN: Gunakan ?? [] untuk mencegah Undefined array key "matrix"
                            foreach ($data['matrix'] ?? [] as $i => $row):
                            ?>
                            <tr>
                                <td class="text-start fw-bold"><?= $alternatives[$i] ?? 'N/A' ?></td>
                                <?php foreach ($row as $value): ?>
                                <td><?= htmlspecialchars($value) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-bold mt-4 mb-3">2. Matriks Ternormalisasi</h5>
                <div class="table-responsive">
                    <table class="table table-custom table-bordered">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                <?php foreach ($criteriaNames as $crit): ?>
                                <th><?= htmlspecialchars($crit) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // PERBAIKAN: Gunakan ?? [] untuk mencegah Undefined array key "normalized"
                            foreach ($data['normalized'] ?? [] as $i => $row): ?>
                            <tr>
                                <td class="text-start fw-bold"><?= $alternatives[$i] ?? 'N/A' ?></td>
                                <?php foreach ($row as $value): ?>
                                <td><?= formatNumber($value) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-bold mt-4 mb-3">3. Matriks Ternormalisasi Terbobot</h5>
                <div class="table-responsive">
                    <table class="table table-custom table-bordered">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                <?php foreach ($criteriaNames as $crit): ?>
                                <th><?= htmlspecialchars($crit) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // PERBAIKAN: Gunakan ?? [] untuk mencegah Undefined array key "weighted"
                            foreach ($data['weighted'] ?? [] as $i => $row): ?>
                            <tr>
                                <td class="text-start fw-bold"><?= $alternatives[$i] ?? 'N/A' ?></td>
                                <?php foreach ($row as $value): ?>
                                <td><?= formatNumber($value) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-bold mt-4 mb-3">4. Solusi Ideal Positif & Negatif</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <h6 class="fw-bold">Solusi Ideal Positif (A+)</h6>
                            <ul class="mb-0">
                                <?php foreach ($data['ideal_positive'] ?? [] as $i => $value): ?>
                                <li><?= htmlspecialchars($criteriaNames[$i] ?? 'Kriteria') ?>: <?= formatNumber($value) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <h6 class="fw-bold">Solusi Ideal Negatif (A-)</h6>
                            <ul class="mb-0">
                                <?php foreach ($data['ideal_negative'] ?? [] as $i => $value): ?>
                                <li><?= htmlspecialchars($criteriaNames[$i] ?? 'Kriteria') ?>: <?= formatNumber($value) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mt-4 mb-3">5. Jarak & Skor Preferensi</h5>
                <div class="table-responsive">
                    <table class="table table-custom table-bordered">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                <th>D+ (Jarak ke A+)</th>
                                <th>D- (Jarak ke A-)</th>
                                <th>Skor (Ci)</th>
                                <th>Ranking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rank = 1;
                            // Gunakan ?? [] untuk menghindari warning jika 'ranked' kosong
                            foreach ($data['ranked'] ?? [] as $alt => $score):
                                $index = array_search($alt, $alternatives);
                                $d_pos = $data['distance_positive'][$index] ?? 0;
                                $d_neg = $data['distance_negative'][$index] ?? 0;
                            ?>
                            <tr>
                                <td class="text-start fw-bold"><?= $alt ?></td>
                                <td><?= formatNumber($d_pos) ?></td>
                                <td><?= formatNumber($d_neg) ?></td>
                                <td><span class="badge bg-<?= $color ?>"><?= formatNumber($score) ?></span></td>
                                <td><strong>#<?= $rank++ ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }

        // --- PANGGIL FUNGSI UNTUK MENAMPILKAN DETAIL TOPSIS SETIAP KATEGORI ---
        // Gunakan ?? [] untuk memastikan array kosong jika kunci tidak ada
        displayTOPSISDetail(
            'TOPSIS: Minat & Bakat',
            '🎯',
            $results['minat_bakat'] ?? [],
            ['Menghitung', 'Menggambar', 'Menulis', 'Membaca'],
            'primary'
        );

        displayTOPSISDetail(
            'TOPSIS: Tema Skripsi',
            '📚',
            $results['tema_skripsi'] ?? [],
            ['Penerapan Algoritma', 'Game', 'SI', 'Media Pembelajaran'],
            'success'
        );

        displayTOPSISDetail(
            'TOPSIS: Pekerjaan',
            '💼',
            $results['pekerjaan'] ?? [],
            ['Programmer', 'Animator', 'Wirausaha', 'Admin'],
            'warning'
        );

        // TOPSIS Global menggunakan nama kategori sebagai kriteria
        displayTOPSISDetail(
            'TOPSIS GLOBAL (Final)',
            '🏆',
            $results['global'] ?? [],
            ['Minat Bakat', 'Tema Skripsi', 'Pekerjaan'],
            'danger'
        );
        ?>

        <div class="text-center mt-5">
            <a href="hasil.php" class="btn btn-primary btn-lg rounded-pill px-5 me-3">← Kembali ke Hasil</a>
            <a href="input.php" class="btn btn-outline-secondary btn-lg rounded-pill px-5">🔄 Tes Ulang</a>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small">Perhitungan dilakukan pada: <?= $results['timestamp'] ?? 'N/A' ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>