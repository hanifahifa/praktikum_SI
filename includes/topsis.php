<?php
/**
 * TOPSIS CALCULATION ENGINE
 * Fungsi utama untuk menghitung TOPSIS
 * * - Mengimplementasikan AHP-TOPSIS bertingkat (Two-Level TOPSIS): 
 * 1. Level 1: Menggunakan 4 sub-kriteria detail dengan bobot AHP sub-kriteria untuk mendapatkan skor per kriteria utama (Minat, Skripsi, Pekerjaan).
 * 2. Level 2: Menggunakan 3 skor kriteria utama (hasil Level 1) sebagai input untuk TOPSIS Global, dengan bobot AHP Global.
 */

require_once 'helper.php';

/**
 * Hitung TOPSIS lengkap
 * (Fungsi ini tetap generik dan tidak diubah, mengandalkan fungsi-fungsi di helper.php)
 * * @param array $matrix - Matriks keputusan [alternatif][kriteria]
 * @param array $weights - Bobot kriteria
 * @param array $alternatives - Nama alternatif (MV, Pemprog, SC)
 * @return array - Hasil perhitungan lengkap
 */
function calculateTOPSIS($matrix, $weights, $alternatives = ['MV', 'Pemprog', 'SC']) {
    // 1. Normalisasi matriks
    $normalized = normalizeMatrix($matrix);
    
    // 2. Matriks ternormalisasi terbobot
    $weighted = weightedNormalizedMatrix($normalized, $weights);
    
    // 3. Solusi ideal positif dan negatif
    $ideal = idealSolutions($weighted);
    
    // 4. Hitung jarak
    $distances = calculateDistances($weighted, $ideal['positive'], $ideal['negative']);
    
    // 5. Hitung skor preferensi
    $scores = preferenceScore($distances['positive'], $distances['negative']);
    
    // 6. Buat hasil dengan nama alternatif
    $results = [];
    foreach ($alternatives as $i => $alt) {
        // Pastikan key MV, Pemprog, SC selalu ada
        $results[$alt] = $scores[$i] ?? 0; 
    }
    
    // 7. Ranking
    $ranked = rankAlternatives($results);
    
    return [
        'matrix' => $matrix,
        'normalized' => $normalized,
        'weighted' => $weighted,
        'ideal_positive' => $ideal['positive'],
        'ideal_negative' => $ideal['negative'],
        'distance_positive' => $distances['positive'],
        'distance_negative' => $distances['negative'],
        'scores' => $results,
        'ranked' => $ranked,
        'best' => array_key_first($ranked),
        'best_score' => reset($ranked)
    ];
}


/**
 * Hitung TOPSIS untuk setiap kategori sub-kriteria (Level 1)
 * Matriks: 3 Alternatif (MV, Pemprog, SC) x 4 Sub-kriteria
 * * @param array $userInput - Semua input dari user (36 skor)
 * @param array $weights - Bobot AHP Sub-kriteria (4 bobot: ahp_minat_bakat, dll.)
 * @param string $category - 'minat', 'skripsi', atau 'pekerjaan'
 * @return array - Hasil perhitungan lengkap (termasuk skor akhir MV, Pemprog, SC)
 */
function topsisSubKriteria($userInput, $weights, $category) {
    // Kunci alternatif input dari formulir (sesuai dengan input.php)
    // Menggunakan 'prog' karena di input.php, field dinamai 'minat_prog_X'
    $inputAltKeys = ['mv', 'prog', 'sc']; 
    $outputAlternatives = ['MV', 'Pemprog', 'SC'];
    
    $matrix = []; // Matriks 3x4 (Alternatif x Sub-kriteria)

    // Membangun Matriks Keputusan 3x4
    foreach ($inputAltKeys as $altKey) { 
        $row = [];
        for ($j = 1; $j <= 4; $j++) {
            // Field name: minat_mv_1, skripsi_prog_2, pekerjaan_sc_4
            $fieldName = "{$category}_{$altKey}_$j";
            // Ambil skor input. Asumsi skor 1 jika tidak ada
            $row[] = isset($userInput[$fieldName]) ? $userInput[$fieldName] : 1; 
        }
        $matrix[] = $row;
    }
    
    // Bobot yang digunakan adalah bobot AHP dari sub-kriteria
    // Bobot (weights) harus cocok urutannya dengan kolom (sub-kriteria 1-4)
    return calculateTOPSIS($matrix, $weights, $outputAlternatives);
}


/**
 * Proses lengkap dari input user (36 input detail) sampai hasil akhir
 * @param array $userInput - Semua input dari form (36 input detail)
 * @param array $ahpWeights - Semua bobot AHP (Minat, Skripsi, Pekerjaan, Global)
 * @return array - Hasil lengkap semua perhitungan
 */
function processFullTOPSIS($userInput, $ahpWeights) {
    // Level 1
    $resultMinat = topsisSubKriteria($userInput, $ahpWeights['minat_bakat'], 'minat');
    $resultSkripsi = topsisSubKriteria($userInput, $ahpWeights['tema_skripsi'], 'skripsi');
    $resultPekerjaan = topsisSubKriteria($userInput, $ahpWeights['pekerjaan'], 'pekerjaan');

    // Level 2 (Global)
    $globalMatrix = [
        [
            $resultMinat['scores']['MV'], 
            $resultSkripsi['scores']['MV'], 
            $resultPekerjaan['scores']['MV']
        ],
        [
            $resultMinat['scores']['Pemprog'], 
            $resultSkripsi['scores']['Pemprog'], 
            $resultPekerjaan['scores']['Pemprog']
        ],
        [
            $resultMinat['scores']['SC'], 
            $resultSkripsi['scores']['SC'], 
            $resultPekerjaan['scores']['SC']
        ]
    ];
    
    $globalWeights = $ahpWeights['global'];
    $resultGlobal = calculateTOPSIS($globalMatrix, $globalWeights, ['MV', 'Pemprog', 'SC']);

    // Simpan hasil lengkap
    return [
        'minat_bakat' => $resultMinat,
        'tema_skripsi' => $resultSkripsi,
        'pekerjaan' => $resultPekerjaan,
        'global' => $resultGlobal, // ← inilah perbaikan penting
        'user_input' => $userInput,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

?>