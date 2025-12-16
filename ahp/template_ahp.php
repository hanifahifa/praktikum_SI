<?php 
// FILE: template_ahp.php
// (CSS dan HTML Wrapper)

function render_template_start($title, $subtitle) {
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>'.$title.'</title>
        <style>
            @import url(\'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap\');
            body { font-family: \'Poppins\', sans-serif; margin: 0; background-color: #f0f4f9; color: #333; }
            .header { background-color: #e6e6fa; color: #4b0082; padding: 40px 20px; text-align: center; margin-bottom: -20px; }
            .header h1 { font-size: 30px; font-weight: 700; color: #6a05ad; margin: 0; }
            .header p { font-size: 16px; color: #8a2be2; margin: 5px 0 0; }
            .container { max-width: 1000px; margin: 0 auto 30px; padding: 30px; background-color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; }
            .ahp-section { margin-bottom: 40px; }
            .section-title { display: flex; align-items: center; font-size: 26px; font-weight: 700; color: #4b0082; margin-bottom: 25px; }
            .step-number { background-color: #6a05ad; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 15px; flex-shrink: 0; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
            .input-area, .results-area { display: block; width: 100%; margin-bottom: 30px; }
            .form-title { color: #8a2be2; font-size: 20px; margin-top: 0; }
            .instruction { background-color: #f8f8ff; color: #6a05ad; padding: 12px; border-left: 4px solid #8a2be2; margin-bottom: 20px; font-size: 15px; }
            .ahp-form table.input-table { width: 100%; min-width: 700px; border-collapse: collapse; font-size: 15px; } 
            .ahp-form th, .ahp-form td { padding: 12px 8px; border: 1px solid #ddd; }
            .ahp-form th { background-color: #8a2be2; color: white; border-color: #6a05ad; }
            .ahp-form td:first-child { font-weight: 600; text-align: left; background-color: #f8f8ff; }
            .diag-input, .recip-input, .main-input { width: 80px; box-sizing: border-box; text-align: center; padding: 8px 5px; font-size: 15px; border-radius: 5px; }
            .diag-input { background-color: #eee; border: 1px solid #ccc; }
            .recip-input { background-color: #f0e68c; border: 1px solid #f0e68c; color: #333; }
            .main-input { border: 2px solid #6a05ad; font-weight: 600; background-color: #fff; }
            .calculate-btn { background-color: #6a05ad; color: white; padding: 12px 25px; border: none; border-radius: 25px; font-size: 17px; font-weight: 600; cursor: pointer; transition: background-color 0.3s; margin-top: 20px; }
            .calculate-btn:hover { background-color: #4b0082; }
            .result-table { border: none; width: 70%; min-width: 450px; border-collapse: collapse; font-size: 16px; }
            .results-area th { background-color: #9370db; color: white; border: none; padding: 12px; }
            .results-area td { border: 1px solid #e0e0e0; padding: 10px; text-align: center; }
            .results-area td:first-child { font-weight: 600; text-align: left; background-color: #f8f8ff; }
            .result-title { color: #8a2be2; font-size: 20px; margin-top: 0; }
            .percentage-box { background-color: #ccf; color: #4b0082; padding: 5px 10px; border-radius: 15px; font-weight: 700; font-size: 15px; display: inline-block; }
            .high-percent { background-color: #d4edda; color: #155724; }
            .cr-box { padding: 15px 20px; border-radius: 8px; margin-top: 20px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; width: 70%; min-width: 450px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
            .cr-consistent { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
            .cr-inconsistent { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
            .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; }
            .consistent-badge { background-color: #28a745; color: white; }
            .inconsistent-badge { background-color: #dc3545; color: white; }
            .nav-link { display: inline-block; margin: 0 5px; padding: 8px 15px; background-color: #9370db; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s; font-size: 14px; }
            .nav-link:hover { background-color: #6a05ad; }
            .nav-container { text-align: center; padding: 15px; background-color: #fff; border-bottom: 1px solid #eee; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Detail Perhitungan AHP</h1>
        </div>
        <div class="nav-container">
            <a href="ahp_step1_global.php" class="nav-link">Kriteria</a>
            <a href="ahp_step2_minat.php" class="nav-link">Minat Bakat</a>
            <a href="ahp_step3_tema.php" class="nav-link">Tema Skripsi</a>
            <a href="ahp_step4_pekerjaan.php" class="nav-link">Pekerjaan</a>
            <a href="ahp_step5_final.php" class="nav-link">Hasil Akhir</a>
        </div>
        <div class="container">';
}

function render_template_end() {
    echo '</div></body></html>';
}

/**
 * Fungsi untuk menampilkan form dan hasil perhitungan
 */
function render_ahp_content($title, $prefix, $criteria, $result, $step) {
    global $IR_MAP;

    // Ambil data input sebelumnya dari POST (untuk mempertahankan data)
    $input_values = [];
    foreach ($criteria as $i => $row_name) {
        foreach ($criteria as $j => $col_name) {
            if ($i < $j) {
                $input_name = $prefix . $i . '_' . $j;
                $input_values[$input_name] = isset($_POST[$input_name]) ? htmlspecialchars($_POST[$input_name]) : '';
            }
        }
    }
    
    // Tentukan ukuran matriks dan IR
    $n = count($criteria);
    $IR = $IR_MAP[$n] ?? 0;
    
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
                                    <th><?php echo str_replace('_', ' ', ucwords($k, '_')); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($criteria as $i => $row_name): ?>
                                <tr>
                                    <td><?php echo str_replace('_', ' ', ucwords($row_name, '_')); ?></td>
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
                <button type="submit" class="calculate-btn">Hitung & Simpan Bobot</button>
            </form>
        </div>

        <?php 
        if ($result && !isset($result['error']) && isset($result['weights'])): 
            $weights = $result['weights'];
            $cr = $result['CR']; 
            $lambda_max = $result['lambda_max'];
            $ci = $result['CI'];
        ?>
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
                    <?php foreach ($weights as $kriteria => $weight): ?>
                        <tr>
                            <td><?php echo str_replace('_', ' ', ucwords($kriteria, '_')); ?></td>
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
            <div class="cr-box <?php echo ($cr > 0.1) ? 'cr-inconsistent' : 'cr-consistent'; ?>">
                Consistency Ratio (CR): **<?php echo number_format($cr, 4); ?>**
                <?php if ($cr <= 0.1): ?>
                    <span class="status-badge consistent-badge">✓ Konsisten</span>
                <?php else: ?>
                    <span class="status-badge inconsistent-badge">✗ Tidak Konsisten</span>
                <?php endif; ?>
            </div>
            <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                $\lambda_{\max}$: <?php echo number_format($lambda_max, 9); ?> | CI: <?php echo number_format($ci, 9); ?>
            </div>
        </div>
        <?php elseif ($result && isset($result['error'])): ?>
             <p class="cr-box cr-inconsistent">Error: <?php echo $result['error']; ?></p>
        <?php endif; ?>
    </section>
    <?php
}
?>