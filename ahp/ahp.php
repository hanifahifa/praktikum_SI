<?php
// Tentukan nilai Random Index (IR)
$IR_MAP = [
    3 => 0.58, 
    4 => 0.90, 
];

/**
 * Fungsi inti untuk menghitung Bobot Prioritas (Eigenvector), Lambda Max, CI, dan CR.
 */
function calculate_ahp($pairwise_matrix, $IR) {
    $criteria_names = array_keys($pairwise_matrix);
    $n = count($criteria_names);

    if ($n < 2 || $IR === 0) {
        return ['error' => 'Ukuran matriks atau Random Index tidak valid.'];
    }

    // 1. Normalisasi Matriks (Penjumlahan Kolom)
    $column_sums = array_fill(0, $n, 0);

    foreach ($criteria_names as $i => $row_name) {
        foreach ($criteria_names as $j => $col_name) {
            $column_sums[$j] += $pairwise_matrix[$row_name][$col_name];
        }
    }

    // Normalisasi dan hitung rata-rata baris (Bobot Prioritas)
    $weights = [];
    foreach ($criteria_names as $i => $row_name) {
        $sum_of_normalized_row = 0;
        foreach ($criteria_names as $j => $col_name) {
            $normalized_value = $pairwise_matrix[$row_name][$col_name] / $column_sums[$j];
            $sum_of_normalized_row += $normalized_value;
        }
        $weights[$row_name] = $sum_of_normalized_row / $n;
    }

    // 2. Perhitungan Konsistensi
    $lambda_max_total = 0;
    
    foreach ($criteria_names as $row_name) {
        $weighted_sum = 0;
        foreach ($criteria_names as $col_name) {
            $weighted_sum += $pairwise_matrix[$row_name][$col_name] * $weights[$col_name];
        }
        
        $lambda_per_row = ($weights[$row_name] != 0) ? $weighted_sum / $weights[$row_name] : 0; 
        $lambda_max_total += $lambda_per_row;
    }
    
    $lambda_max = $lambda_max_total / $n;
    $CI = ($lambda_max - $n) / ($n - 1);
    $CR = $CI / $IR;

    return [
        'weights' => $weights,
        'lambda_max' => $lambda_max,
        'CI' => $CI,
        'CR' => $CR,
        'n' => $n,
        'IR' => $IR,
        'input_matrix' => $pairwise_matrix
    ];
}

/**
 * Fungsi untuk mengolah input dari form.
 */
function process_input($form_prefix, $criteria_names) {
    $n = count($criteria_names);
    $matrix = [];

    // Proses input utama (di atas diagonal)
    foreach ($criteria_names as $i => $row_key) {
        $matrix[$row_key] = [];
        foreach ($criteria_names as $j => $col_key) {
            if ($i == $j) {
                $matrix[$row_key][$col_key] = 1.0;
            } elseif ($i < $j) {
                $input_name = $form_prefix . $i . '_' . $j;
                $value = isset($_POST[$input_name]) ? floatval($_POST[$input_name]) : 1.0;
                $matrix[$row_key][$col_key] = $value;
            }
        }
    }
    
    // Hitung nilai resiprokal (di bawah diagonal)
    foreach ($criteria_names as $i => $row_key) {
        foreach ($criteria_names as $j => $col_key) {
            if ($i > $j) {
                $value_atas = $matrix[$col_key][$row_key];
                $matrix[$row_key][$col_key] = ($value_atas != 0) ? 1.0 / $value_atas : 0;
            }
        }
    }

    return $matrix;
}

// =======================================================
// DEFINISI KRITERIA & PROSES INPUT
// =======================================================
$global_criteria = ['Minat Bakat', 'Tema Skripsi', 'Pekerjaan'];
$minat_bakat_sub = ['Menghitung', 'Menggambar', 'Menulis', 'Membaca'];
$tema_skripsi_sub = ['Penerapan Algoritma', 'Game', 'SI', 'Media Pembelajaran'];
$pekerjaan_sub = ['Programmer', 'Animator', 'Wirausaha', 'Admin'];

$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Proses Matriks
    $matrix_global = process_input('global_', $global_criteria);
    $matrix_minat = process_input('minat_', $minat_bakat_sub);
    $matrix_tema = process_input('tema_', $tema_skripsi_sub);
    $matrix_pekerjaan = process_input('pekerjaan_', $pekerjaan_sub);
    
    // Lakukan Perhitungan AHP
    $results['global'] = calculate_ahp($matrix_global, $IR_MAP[3]);
    $results['minat'] = calculate_ahp($matrix_minat, $IR_MAP[4]);
    $results['tema'] = calculate_ahp($matrix_tema, $IR_MAP[4]);
    $results['pekerjaan'] = calculate_ahp($matrix_pekerjaan, $IR_MAP[4]);
    
    // 5. PERHITUNGAN BOBOT GLOBAL AKHIR
    $global_weights = [];
    
    if (!isset($results['global']['error']) && !isset($results['minat']['error']) && !isset($results['tema']['error']) && !isset($results['pekerjaan']['error'])) 
    {
        $Wg = $results['global']['weights']; 
        $Wmb = $results['minat']['weights'];
        $Wts = $results['tema']['weights'];
        $Wp = $results['pekerjaan']['weights'];

        // Menghitung Bobot Global Akhir
        foreach ($Wmb as $sub_kriteria => $w) {
            $global_weights[$sub_kriteria] = $w * $Wg['Minat Bakat'];
        }
        foreach ($Wts as $sub_kriteria => $w) {
            $global_weights[$sub_kriteria] = $w * $Wg['Tema Skripsi'];
        }
        foreach ($Wp as $sub_kriteria => $w) {
            $global_weights[$sub_kriteria] = $w * $Wg['Pekerjaan'];
        }
        
        arsort($global_weights);
        $results['global_final'] = $global_weights;
    }
}

/**
 * Fungsi untuk menampilkan form dan hasil perhitungan
 */
function render_ahp_section($title, $prefix, $criteria, $result, $size, $step) {
    global $IR_MAP;
    $n = count($criteria);
    $IR = $IR_MAP[$n] ?? 0;
    
    $input_values = [];
    foreach ($criteria as $i => $row_name) {
        foreach ($criteria as $j => $col_name) {
            if ($i < $j) {
                $input_name = $prefix . $i . '_' . $j;
                $input_values[$input_name] = isset($_POST[$input_name]) ? htmlspecialchars($_POST[$input_name]) : '';
            }
        }
    }
    
    ?>
    <section class="ahp-section step-<?php echo $step; ?>">
        <h2 class="section-title">
            <span class="step-number"><?php echo $step; ?></span> 
            <?php echo $title; ?>
        </h2>
        
        <div class="input-area block-layout">
            <h3 class="form-title">Input Matriks Perbandingan Berpasangan</h3>
            <p class="instruction">Isi hanya sel non-diagonal di atas diagonal utama. (IR <?php echo $IR; ?>)</p>
            <form method="post" class="ahp-form">
                <div style="overflow-x: auto;"> 
                    <table class="input-table">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <?php foreach ($criteria as $k): ?>
                                    <th><?php echo $k; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($criteria as $i => $row_name): ?>
                                <tr>
                                    <td><?php echo $row_name; ?></td>
                                    <?php foreach ($criteria as $j => $col_name): 
                                        $input_name = $prefix . $i . '_' . $j;
                                    ?>
                                        <td>
                                            <?php if ($i == $j): ?>
                                                <input type="text" value="1" readonly class="diag-input">
                                            <?php elseif ($i < $j): ?>
                                                <input type="number" step="any" min="0.1" name="<?php echo $input_name; ?>" value="<?php echo $input_values[$input_name]; ?>" required class="main-input">
                                            <?php else: 
                                                $input_atas = $prefix . $j . '_' . $i;
                                                $value_atas = isset($_POST[$input_atas]) ? floatval($_POST[$input_atas]) : 1;
                                                $reciprocal_value = ($value_atas != 0) ? 1 / $value_atas : 0;
                                            ?>
                                                <input type="text" value="<?php echo number_format($reciprocal_value, 3); ?>" readonly class="recip-input">
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="calculate-btn">Hitung Bobot</button>
            </form>
        </div>

        <?php if ($result && !isset($result['error'])): ?>
        <div class="results-area block-layout">
            <h3 class="result-title">Hasil Bobot Prioritas</h3>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Bobot</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['weights'] as $kriteria => $weight): ?>
                        <tr>
                            <td><?php echo $kriteria; ?></td>
                            <td><?php echo number_format($weight, 4); ?></td>
                            <td>
                                <span class="percentage-box <?php echo ($weight > 0.5) ? 'high-percent' : ''; ?>">
                                    <?php echo number_format($weight * 100, 2); ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cr-box <?php echo ($result['CR'] > 0.1) ? 'cr-inconsistent' : 'cr-consistent'; ?>">
                Consistency Ratio (CR): **<?php echo number_format($result['CR'], 4); ?>**
                <?php if ($result['CR'] <= 0.1): ?>
                    <span class="status-badge consistent-badge">✓ Konsisten</span>
                <?php else: ?>
                    <span class="status-badge inconsistent-badge">✗ Tidak Konsisten</span>
                <?php endif; ?>
            </div>
        </div>
        <?php elseif ($result && isset($result['error'])): ?>
             <p class="error-message">Error: <?php echo $result['error']; ?></p>
        <?php endif; ?>
    </section>
    <?php
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lengkap AHP Interaktif - Final</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        /* GENERAL STYLES */
        body { 
            font-family: 'Poppins', sans-serif; 
            margin: 0; 
            background-color: #f0f4f9; 
            color: #333; 
        }
        .header {
            background-color: #e6e6fa; 
            color: #4b0082; 
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #6a05ad; 
            margin: 0;
            margin-top: 10px;
        }
        .header p {
            font-size: 16px;
            color: #8a2be2;
            margin: 5px 0 0;
        }
        .transparency-tag {
            background-color: #9370db; 
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            display: inline-block;
            text-transform: uppercase;
        }

        /* CONTAINER & SECTION STYLES */
        .container { 
            max-width: 1200px; 
            margin: -20px auto 30px; 
            padding: 30px; 
            background-color: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border-radius: 12px;
        }
        .ahp-section { 
            margin-bottom: 40px; 
            padding-bottom: 30px;
            border-bottom: 2px dashed #e0e0e0;
        }
        .section-title {
            display: flex;
            align-items: center;
            font-size: 26px;
            font-weight: 700;
            color: #4b0082;
            margin-bottom: 25px;
        }
        .step-number {
            background-color: #6a05ad;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* TATA LETAK DI BAWAH (BLOCK LAYOUT) */
        .input-area, .results-area {
            display: block;
            width: 100%; 
            margin-bottom: 30px; 
        }
        
        /* FORM & INPUT STYLES */
        .form-title { color: #8a2be2; font-size: 20px; margin-top: 0; }
        .instruction { background-color: #f8f8ff; color: #6a05ad; padding: 12px; border-left: 4px solid #8a2be2; margin-bottom: 20px; font-size: 15px; }
        .ahp-form table.input-table { 
            width: 100%; 
            min-width: 700px; 
            border-collapse: collapse;
            font-size: 15px;
        } 
        .ahp-form th, .ahp-form td { padding: 12px 8px; border: 1px solid #ddd; }
        .ahp-form th { background-color: #8a2be2; color: white; border-color: #6a05ad; }
        .ahp-form td:first-child { font-weight: 600; text-align: left; background-color: #f8f8ff; }

        .diag-input, .recip-input, .main-input { 
            width: 80px; 
            box-sizing: border-box; 
            text-align: center;
            padding: 8px 5px; /* Lebih besar */
            font-size: 15px; /* Lebih besar */
            border-radius: 5px;
        }
        .diag-input { background-color: #eee; border: 1px solid #ccc; }
        .recip-input { background-color: #f0e68c; border: 1px solid #f0e68c; color: #333; }
        .main-input { border: 2px solid #6a05ad; font-weight: 600; background-color: #fff; }

        .calculate-btn { 
            background-color: #6a05ad; 
            color: white; 
            padding: 12px 25px; 
            border: none; 
            border-radius: 25px; 
            font-size: 17px; 
            font-weight: 600;
            cursor: pointer; 
            transition: background-color 0.3s; 
            margin-top: 20px;
        }
        .calculate-btn:hover { background-color: #4b0082; }

        /* RESULT STYLES */
        .result-table { 
            border: none; 
            width: 70%; /* Lebih besar dari sebelumnya */
            min-width: 450px;
            border-collapse: collapse;
            font-size: 16px;
        }
        .results-area th { background-color: #9370db; color: white; border: none; padding: 12px; }
        .results-area td { border: 1px solid #e0e0e0; padding: 10px; text-align: center; }
        .results-area td:first-child { font-weight: 600; text-align: left; background-color: #f8f8ff; }
        .result-title { color: #8a2be2; font-size: 20px; margin-top: 0; }
        
        .percentage-box {
            background-color: #ccf;
            color: #4b0082;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 15px;
            display: inline-block;
        }
        .high-percent {
            background-color: #d4edda; /* Hijau muda */
            color: #155724; /* Hijau tua */
        }

        /* CR BOX STYLES */
        .cr-box {
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 70%; /* Disesuaikan dengan tabel hasil */
            min-width: 450px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .cr-consistent {
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .cr-inconsistent {
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }
        .consistent-badge { background-color: #28a745; color: white; }
        .inconsistent-badge { background-color: #dc3545; color: white; }

        /* FINAL GLOBAL RESULT STYLES */
        .final-results { 
            background-color: #f8f8ff; 
            border: 2px solid #8a2be2; 
            padding: 30px; 
            border-radius: 10px; 
            margin-top: 30px; 
        }
        .final-results h2 { color: #6a05ad; font-size: 26px; border-bottom: none; }
        .final-results table { width: 70%; min-width: 450px; }
        .final-results table th { background-color: #4b0082; }
        .final-results table td:nth-child(2) { font-weight: 700; color: #4b0082; }
        .final-results table td:nth-child(3) { font-weight: 700; background-color: #ccf2ff; color: #4b0082; }
        .final-results .step-number { background-color: #4b0082; }
    </style>
</head>
<body>

    <div class="header">
        <span class="transparency-tag">Transparansi Perhitungan</span>
        <h1>Detail Lengkap AHP</h1>
        <p>Semua langkah perhitungan bobot ditampilkan secara transparan</p>
    </div>

    <div class="container">
        <h2 style="color: #4b0082; font-size: 28px; border-bottom: 2px solid #8a2be2; padding-bottom: 10px;">
            <span class="step-number" style="background-color: #4b0082;">1-4</span> Analytical Hierarchy Process (AHP)
        </h2>
        <p style="margin-top: -10px; margin-bottom: 30px;">Bobot kriteria ditentukan menggunakan metode AHP berdasarkan perbandingan berpasangan.</p>

        <?php 
        // 1. Kriteria Global (3x3)
        $result_global = $results['global'] ?? null;
        render_ahp_section('Bobot Kriteria Global (Level 1)', 'global_', $global_criteria, $result_global, '3x3', 1);
        ?>

        <?php
        // 2. Subkriteria Minat Bakat (4x4)
        $result_minat = $results['minat'] ?? null;
        render_ahp_section('Bobot Kriteria Minat & Bakat (Level 2)', 'minat_', $minat_bakat_sub, $result_minat, '4x4', 2);
        ?>

        <?php
        // 3. Subkriteria Tema Skripsi (4x4)
        $result_tema = $results['tema'] ?? null;
        render_ahp_section('Bobot Kriteria Tema Skripsi (Level 2)', 'tema_', $tema_skripsi_sub, $result_tema, '4x4', 3);
        ?>
        
        <?php
        // 4. Subkriteria Pekerjaan (4x4)
        $result_pekerjaan = $results['pekerjaan'] ?? null;
        render_ahp_section('Bobot Kriteria Pekerjaan (Level 2)', 'pekerjaan_', $pekerjaan_sub, $result_pekerjaan, '4x4', 4);
        ?>

        <?php if (isset($results['global_final'])): ?>
        <div class="final-results">
            <h2 class="section-title"><span class="step-number">5</span> Bobot Global Akhir (Ranking Alternatif)</h2>
            <p style="margin-top: -10px;">Bobot Global (WG) adalah hasil perkalian Bobot Prioritas Level 1 dengan Bobot Prioritas Level 2 yang bersesuaian.</p>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Alternatif/Subkriteria</th>
                        <th>Bobot Global Akhir (WG)</th>
                        <th>Ranking</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($results['global_final'] as $kriteria => $weight): ?>
                        <tr>
                            <td><?php echo $kriteria; ?></td>
                            <td><?php echo number_format($weight, 9); ?></td>
                            <td><span class="percentage-box high-percent"><?php echo $rank++; ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>

</body>
</html>