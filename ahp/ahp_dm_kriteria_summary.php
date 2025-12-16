<?php
// FILE: ahp_dm_kriteria_summary.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek akses: Hanya DM yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dm') {
    header("Location: login.php");
    exit();
}

include 'ahp_core.php';

// ==========================================================
// 1. KONFIGURASI & GET DATA
// ==========================================================
$dm_id      = $_SESSION['dm_id'];
$dm_nama    = $_SESSION['user_nama'];
$dm_role    = $_SESSION['dm_role_label'];

$level_name = "kriteria_dm_" . $dm_id; 
$title      = "Perbandingan Berpasangan (Decision Maker)";
$criteria   = ['minat_bakat', 'tema_skripsi', 'pekerjaan']; 
$prefix     = 'dm_crit_'; 
$matrix_size = count($criteria);

// IR Map Manual
$IR_MAP_MANUAL = [
    1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 
    5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 
    9 => 1.45, 10 => 1.49
];
$IR = $IR_MAP_MANUAL[$matrix_size] ?? 0.58;

$result = null;

// QUERY: Ambil data User + Bobot dari tabel ahp_weights_kriteria (JOIN)
$query_check = "
    SELECT dm.id, w.minat_bakat, w.tema_skripsi, w.pekerjaan, w.cr_kriteria, w.is_consistent
    FROM decision_makers dm
    LEFT JOIN ahp_weights_kriteria w ON dm.input_matrix_id = w.input_matrix_id
    WHERE dm.id = ?
";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("i", $dm_id);
$stmt_check->execute();
$current_data = $stmt_check->get_result()->fetch_assoc();

// Jika sudah ada data, tampilkan
if ($current_data && !is_null($current_data['minat_bakat'])) {
    $result = [
        'weights' => [
            'minat_bakat' => $current_data['minat_bakat'],
            'tema_skripsi' => $current_data['tema_skripsi'],
            'pekerjaan' => $current_data['pekerjaan']
        ],
        'CR' => $current_data['cr_kriteria']
    ];
}

// ==========================================================
// 2. PROSES INPUT & SIMPAN
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // A. Susun Matriks
    $matrix = [];
    foreach ($criteria as $i => $row_key) {
        foreach ($criteria as $j => $col_key) {
            if ($i == $j) {
                $matrix[$row_key][$col_key] = 1;
            } elseif ($i < $j) {
                $input_name = $prefix . $i . '_' . $j;
                $val = isset($_POST[$input_name]) ? floatval($_POST[$input_name]) : 1;
                if ($val <= 0) $val = 1; 
                $matrix[$row_key][$col_key] = $val;
            } else {
                $upper_val = $matrix[$col_key][$row_key];
                $matrix[$row_key][$col_key] = 1 / $upper_val;
            }
        }
    }

    // B. Hitung AHP
    $result_new = calculate_ahp($matrix, $IR);
    
    if (isset($result_new['error'])) {
        echo "<script>alert('ERROR AHP: " . $result_new['error'] . "');</script>";
    } else {
        
        // C. Simpan ke Database
        $input_id = save_input_matrix($level_name, $matrix);

        if ($input_id) {
            $w = $result_new['weights'];
            $cr = $result_new['CR'];
            $is_consistent = ($cr <= 0.1) ? 1 : 0;

            // --- PERBAIKAN UTAMA: SAFETY MAPPING ---
            // Kita gunakan ?? untuk mengecek apakah key ada. 
            // Jika tidak ada (misal key-nya angka 0,1,2), kita ambil berdasarkan urutan.
            
            // Urutan array $criteria kita adalah: 0=minat_bakat, 1=tema_skripsi, 2=pekerjaan
            $val_mb = $w['minat_bakat'] ?? $w[0] ?? null;
            $val_ts = $w['tema_skripsi'] ?? $w[1] ?? null;
            $val_pj = $w['pekerjaan']   ?? $w[2] ?? null;

            // Validasi Terakhir: Jika masih NULL, set default (untuk mencegah Error SQL)
            if ($val_mb === null) $val_mb = 0.33; 
            if ($val_ts === null) $val_ts = 0.33;
            if ($val_pj === null) $val_pj = 0.33;

            // Cek apakah row sudah ada di tabel bobot
            $check_w = $conn->query("SELECT id FROM ahp_weights_kriteria WHERE input_matrix_id = $input_id");
            
            if ($check_w && $check_w->num_rows > 0) {
                // Update
                $sql_w = "UPDATE ahp_weights_kriteria SET 
                          minat_bakat=?, tema_skripsi=?, pekerjaan=?, cr_kriteria=?, is_consistent=? 
                          WHERE input_matrix_id=?";
                $stmt_w = $conn->prepare($sql_w);
                $stmt_w->bind_param("ddddii", $val_mb, $val_ts, $val_pj, $cr, $is_consistent, $input_id);
            } else {
                // Insert
                $sql_w = "INSERT INTO ahp_weights_kriteria (input_matrix_id, minat_bakat, tema_skripsi, pekerjaan, cr_kriteria, is_consistent) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_w = $conn->prepare($sql_w);
                // Inilah baris yang sebelumnya error, sekarang aman karena variabel $val_... sudah dipastikan tidak null
                $stmt_w->bind_param("iddddi", $input_id, $val_mb, $val_ts, $val_pj, $cr, $is_consistent);
            }
            
            if ($stmt_w->execute()) {
                $stmt_w->close();
                
                // Update User
                $sql_dm = "UPDATE decision_makers SET input_matrix_id = ? WHERE id = ?";
                $stmt_dm = $conn->prepare($sql_dm);
                $stmt_dm->bind_param("ii", $input_id, $dm_id);
                $stmt_dm->execute();
                
                $result = $result_new;
                // Pastikan result weights menggunakan key nama agar tampilan tabel di bawah benar
                $result['weights'] = [
                    'minat_bakat' => $val_mb,
                    'tema_skripsi' => $val_ts,
                    'pekerjaan' => $val_pj
                ];

                echo "<script>alert('Sukses! Bobot berhasil disimpan.');</script>";
            } else {
                echo "<div style='color:red; font-weight:bold;'>SQL ERROR: " . $stmt_w->error . "</div>";
            }

        } else {
             echo "<script>alert('Gagal menyimpan matriks input.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: #f0f4f9; color: #333; }
        .container { max-width: 1000px; margin: 30px auto; padding: 30px; background-color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; }
        .user-info-box { background: #fff; padding: 20px; border-left: 5px solid #6a05ad; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .user-info-box h3 { margin: 0; color: #4b0082; font-size: 18px; }
        .input-table { width: 100%; border-collapse: collapse; font-size: 15px; margin-bottom: 20px; }
        .input-table th { background-color: #8a2be2; color: white; padding: 12px; }
        .input-table td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        .label-cell { font-weight: 600; text-align: left; background-color: #f8f8ff; }
        .main-input { width: 80px; padding: 8px; text-align: center; border: 2px solid #6a05ad; border-radius: 5px; font-weight: 600; }
        .diag-input { width: 80px; padding: 8px; text-align: center; background: #eee; border: 1px solid #ccc; border-radius: 5px; }
        .recip-input { width: 80px; padding: 8px; text-align: center; background: #f0e68c; border: 1px solid #eedc82; border-radius: 5px; }
        .calculate-btn { background-color: #6a05ad; color: white; padding: 12px 25px; border: none; border-radius: 25px; font-size: 16px; font-weight: 600; cursor: pointer; display: inline-block; margin-top: 10px; }
        .logout-btn { background-color: #dc3545; color: white; padding: 10px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; }
        .step-number { background-color: #6a05ad; color: white; border-radius: 50%; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 10px; }
        .section-header { display: flex; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="user-info-box">
        <h3>Halo, <?= htmlspecialchars($dm_nama) ?></h3>
        <p>Role: <?= htmlspecialchars($dm_role) ?></p>
        <p style="margin-top:15px; font-style: italic;">Silakan isi perbandingan kriteria di bawah ini.</p>
    </div>

    <form method="post" action="">
        <div class="section-header">
            <span class="step-number">1</span>
            <h2><?= $title ?></h2>
        </div>

        <h3 style="color: #8a2be2;">Input Matriks Perbandingan Berpasangan</h3>
        <p style="background:#f8f8ff; color:#6a05ad; padding:10px; border-left:4px solid #8a2be2; margin-bottom:20px;">
            Isi hanya sel non-diagonal di atas diagonal utama. (IR <?= $IR ?>)
        </p>

        <div style="overflow-x: auto;">
            <?php
            $input_values = [];
            foreach ($criteria as $i => $row) {
                foreach ($criteria as $j => $col) {
                    if ($i < $j) {
                        $name = $prefix . $i . '_' . $j;
                        $input_values[$name] = isset($_POST[$name]) ? $_POST[$name] : '';
                    }
                }
            }
            ?>
            <table class="input-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <?php foreach ($criteria as $k): ?>
                            <th><?= str_replace('_', ' ', ucwords($k, '_')) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteria as $i => $row_name): ?>
                        <tr>
                            <td class="label-cell"><?= str_replace('_', ' ', ucwords($row_name, '_')) ?></td>
                            <?php foreach ($criteria as $j => $col_name): ?>
                                <td>
                                    <?php if ($i == $j): ?>
                                        <input type="text" value="1" readonly class="diag-input">
                                    <?php elseif ($i < $j): ?>
                                        <?php $name = $prefix . $i . '_' . $j; ?>
                                        <input type="number" step="any" min="0.1" name="<?= $name ?>" 
                                               value="<?= $input_values[$name] ?>" required class="main-input">
                                    <?php else: ?>
                                        <?php 
                                            $name_atas = $prefix . $j . '_' . $i;
                                            $val_atas = isset($_POST[$name_atas]) ? floatval($_POST[$name_atas]) : 1;
                                            $val_recip = ($val_atas > 0) ? 1 / $val_atas : 0;
                                        ?>
                                        <input type="text" value="<?= number_format($val_recip, 3) ?>" readonly class="recip-input">
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <button type="submit" class="calculate-btn">Hitung & Simpan Bobot</button>
    </form>
    
    <?php if ($result && isset($result['weights'])): ?>
        <div style="margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px;">
            <h3 style="color:#4b0082;">Hasil Perhitungan</h3>
            <div style="background: <?= ($result['CR'] <= 0.1) ? '#d4edda' : '#f8d7da' ?>; padding: 15px; border-radius: 8px; display: inline-block; margin-bottom:10px;">
                <strong>CR: <?= number_format($result['CR'], 5) ?></strong> 
                (<?= ($result['CR'] <= 0.1) ? 'KONSISTEN' : 'TIDAK KONSISTEN' ?>)
            </div>
            <table class="input-table" style="width:50%;">
                <thead><tr><th>Kriteria</th><th>Bobot</th></tr></thead>
                <tbody>
                    <?php foreach ($result['weights'] as $k => $v): ?>
                        <tr>
                            <td><?= str_replace('_', ' ', ucwords($k, '_')) ?></td>
                            <td><?= number_format($v, 4) ?> (<?= number_format($v*100, 2) ?>%)</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div style="text-align: center; margin: 40px 0;">
    <a href="login.php" class="logout-btn">Log Out</a>
</div>

</body>
</html>