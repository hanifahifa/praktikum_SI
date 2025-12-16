<?php
// FILE: ahp_step5_final.php
include 'ahp_core.php';
include 'template_ahp.php';

$title = 'Bobot Global Akhir (Ranking Alternatif)';
$step = 5;
$global_weights = [];
$missing_data = false;

// Ambil semua hasil bobot dari database
$res_global = get_ahp_weights('ahp_weights_kriteria')['weights'] ?? null;
$res_minat = get_ahp_weights('ahp_weights_minat_bakat')['weights'] ?? null;
$res_tema = get_ahp_weights('ahp_weights_tema_skripsi')['weights'] ?? null;
$res_pekerjaan = get_ahp_weights('ahp_weights_pekerjaan')['weights'] ?? null;

if ($res_global && $res_minat && $res_tema && $res_pekerjaan) {
    
    $Wg = $res_global; 
    $Wmb = $res_minat;
    $Wts = $res_tema;
    $Wp = $res_pekerjaan;

    // Hitung Bobot Global Akhir = Bobot Kriteria * Bobot Subkriteria
    
    // Minat Bakat
    if (isset($Wg['minat_bakat'])) {
        foreach ($Wmb as $sub_kriteria => $w) {
            $global_weights[$sub_kriteria] = $w * $Wg['minat_bakat'];
        }
    }

    // Tema Skripsi
    if (isset($Wg['tema_skripsi'])) {
        foreach ($Wts as $sub_kriteria => $w) {
            $global_weights[$sub_kriteria] = $w * $Wg['tema_skripsi'];
        }
    }

    // Pekerjaan
    if (isset($Wg['pekerjaan'])) {
        foreach ($Wp as $sub_kriteria => $w) {
            $global_weights[$sub_kriteria] = $w * $Wg['pekerjaan'];
        }
    }
    
    arsort($global_weights);
} else {
    $missing_data = true;
}

render_template_start("Step {$step}: {$title}", "Bobot Akhir dari Seluruh Subkriteria");
?>

<div class="ahp-section step-<?php echo $step; ?>">
    <h2 class="section-title"><span class="step-number"><?php echo $step; ?></span> Bobot Global Akhir (Ranking Alternatif)</h2>
    <p style="margin-top: 10px; margin-bottom: 20px;">Ranking ini diperoleh dari hasil perkalian Bobot Kriteria Utama dengan Bobot Subkriteria yang bersesuaian. </p>

    <?php if ($missing_data): ?>
        <div class="cr-box cr-inconsistent" style="width: 100%;">
            Data perhitungan AHP (Step 1-4) belum lengkap di database. Harap hitung dan simpan semua matriks terlebih dahulu.
        </div>
    <?php else: ?>
        <table class="result-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Alternatif/Subkriteria</th>
                    <th>Bobot Global Akhir (WG)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($global_weights as $kriteria => $weight): ?>
                    <tr>
                        <td><span class="percentage-box high-percent" style="background-color: #6a05ad; color: white;"><?php echo $rank++; ?></span></td>
                        <td><?php echo str_replace('_', ' ', ucwords($kriteria, '_')); ?></td>
                        <td><?php echo number_format($weight, 9); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
render_template_end();
?>