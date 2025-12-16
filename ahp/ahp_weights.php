<?php
/**
 * FILE: ahp/ahp_weights.php
 * File ini mengambil data input dari Para Pakar (Decision Makers),
 * menghitung rata-rata ukur (GDSS), dan menyiapkannya untuk hasil.php
 */

// Pastikan jalur ke ahp_core.php benar (sesuaikan '../' jika perlu)
// Jika file ini ada di folder 'ahp/', dan core ada di folder yang sama:
include_once __DIR__ . '/ahp_core.php';

// Fungsi Helper: Format key database (snake_case) menjadi Title Case (Tampilan)
// Contoh: 'minat_bakat' -> 'Minat Bakat', 'media_pembelajaran' -> 'Media Pembelajaran'
function format_keys_for_topsis($weights) {
    $formatted = [];
    foreach ($weights as $key => $val) {
        $clean_key = ucwords(str_replace('_', ' ', $key));
        $formatted[$clean_key] = $val;
    }
    return $formatted;
}

// Fungsi Helper: Ambil Bobot GDSS (Geometric Mean)
function get_gdss_data($pattern, $size) {
    global $IR_MAP;
    
    // 1. Ambil Matriks Gabungan (Geometric Mean dari semua Pakar)
    // Fungsi calculate_gdss_matrix ada di ahp_core.php
    $matrix = calculate_gdss_matrix($pattern);
    
    // Jika belum ada input pakar sama sekali, return default/kosong
    if (!$matrix) return null;

    // 2. Hitung AHP dari matriks gabungan
    $ir = $IR_MAP[$size] ?? 0.90;
    $res = calculate_ahp($matrix, $ir);

    return $res;
}

// =============================================================
// EKSEKUSI PENGAMBILAN DATA (REAL-TIME)
// =============================================================

// 1. Level 1: Global (3 Kriteria) -> Pola DB: 'kriteria_dm_%'
$res_global = get_gdss_data('kriteria_dm_%', 3);
$ahp_global = format_keys_for_topsis($res_global['weights'] ?? []);

// 2. Level 2: Minat Bakat (4 Sub) -> Pola DB: 'minat_dm_%'
$res_minat = get_gdss_data('minat_dm_%', 4);
$ahp_minat_bakat = format_keys_for_topsis($res_minat['weights'] ?? []);

// 3. Level 2: Tema Skripsi (4 Sub) -> Pola DB: 'tema_dm_%'
$res_tema = get_gdss_data('tema_dm_%', 4);
$ahp_tema_skripsi = format_keys_for_topsis($res_tema['weights'] ?? []);

// 4. Level 2: Pekerjaan (4 Sub) -> Pola DB: 'pekerjaan_dm_%'
$res_pekerjaan = get_gdss_data('pekerjaan_dm_%', 4);
$ahp_pekerjaan = format_keys_for_topsis($res_pekerjaan['weights'] ?? []);


// =============================================================
// SIAPKAN STRUKTUR DATA UNTUK DETAIL.PHP (TRANSPARANSI)
// =============================================================
$ahp_detail = [];

// Populate Global
if ($res_global) {
    $ahp_detail['global'] = [
        'weights' => $ahp_global,
        'CR'      => $res_global['CR'],
        'lambda'  => $res_global['lambda_max']
    ];
}

// Populate Minat
if ($res_minat) {
    $ahp_detail['minat_bakat'] = [
        'weights' => $ahp_minat_bakat,
        'CR'      => $res_minat['CR']
    ];
}

// Populate Tema
if ($res_tema) {
    $ahp_detail['tema_skripsi'] = [
        'weights' => $ahp_tema_skripsi,
        'CR'      => $res_tema['CR']
    ];
}

// Populate Pekerjaan
if ($res_pekerjaan) {
    $ahp_detail['pekerjaan'] = [
        'weights' => $ahp_pekerjaan,
        'CR'      => $res_pekerjaan['CR']
    ];
}
?>