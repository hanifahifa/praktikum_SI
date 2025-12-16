<?php
/**
 * HELPER FUNCTIONS
 * Fungsi-fungsi pembantu untuk perhitungan matematis
 */

/**
 * Normalisasi matriks keputusan (Euclidean)
 * @param array $matrix - Matriks keputusan
 * @return array - Matriks ternormalisasi
 */
function normalizeMatrix($matrix) {
    $normalized = [];
    if (empty($matrix) || empty($matrix[0])) {
        return $normalized; // Return empty array if matrix is empty
    }
    
    $criteriaCount = count($matrix[0]);
    
    // Hitung akar jumlah kuadrat per kolom (denominator untuk normalisasi)
    $sqrtSums = [];
    for ($j = 0; $j < $criteriaCount; $j++) {
        $sumSquares = 0;
        foreach ($matrix as $row) {
            // Pastikan elemen ada dan numerik sebelum pow()
            $sumSquares += pow((float)($row[$j] ?? 0), 2);
        }
        $sqrtSums[$j] = sqrt($sumSquares);
    }
    
    // Normalisasi setiap elemen
    foreach ($matrix as $i => $row) {
        for ($j = 0; $j < $criteriaCount; $j++) {
            $value = (float)($row[$j] ?? 0);
            
            // Cek Division by Zero: jika $sqrtSums[$j]$ nol
            $normalized[$i][$j] = ($sqrtSums[$j] != 0) ? ($value / $sqrtSums[$j]) : 0;
        }
    }
    
    return $normalized;
}

/**
 * Hitung matriks ternormalisasi terbobot
 * @param array $normalized - Matriks ternormalisasi
 * @param array $weights - Bobot kriteria (array asosiatif)
 * @return array - Matriks terbobot
 */
function weightedNormalizedMatrix($normalized, $weights) {
    $weighted = [];
    // Ambil nilai bobot saja dan pastikan urutan sesuai kolom matriks
    $weightsArray = array_values($weights); 
    
    foreach ($normalized as $i => $row) {
        foreach ($row as $j => $value) {
            // Pastikan bobot ada untuk kolom ini
            if (isset($weightsArray[$j])) {
                $weighted[$i][$j] = (float)$value * (float)$weightsArray[$j];
            } else {
                $weighted[$i][$j] = 0; 
            }
        }
    }
    
    return $weighted;
}

/**
 * Hitung solusi ideal positif dan negatif
 * @param array $weighted - Matriks terbobot
 * @return array - ['positive' => [...], 'negative' => [...]]
 */
function idealSolutions($weighted) {
    $positive = [];
    $negative = [];
    
    if (empty($weighted) || empty($weighted[0])) {
        return ['positive' => [], 'negative' => []];
    }

    $criteriaCount = count($weighted[0]);
    
    for ($j = 0; $j < $criteriaCount; $j++) {
        $column = array_column($weighted, $j);
        $positive[$j] = max($column);
        $negative[$j] = min($column);
    }
    
    return [
        'positive' => $positive,
        'negative' => $negative
    ];
}

/**
 * Hitung jarak ke solusi ideal
 * @param array $weighted - Matriks terbobot
 * @param array $idealPositive - Solusi ideal positif
 * @param array $idealNegative - Solusi ideal negatif
 * @return array - ['positive' => [...], 'negative' => [...]]
 */
function calculateDistances($weighted, $idealPositive, $idealNegative) {
    $distPositive = [];
    $distNegative = [];
    
    foreach ($weighted as $i => $row) {
        $sumPosSquares = 0;
        $sumNegSquares = 0;
        
        foreach ($row as $j => $value) {
            // Jarak ke Ideal Positif
            $sumPosSquares += pow((float)$value - (float)($idealPositive[$j] ?? 0), 2);
            // Jarak ke Ideal Negatif
            $sumNegSquares += pow((float)$value - (float)($idealNegative[$j] ?? 0), 2);
        }
        
        $distPositive[$i] = sqrt($sumPosSquares);
        $distNegative[$i] = sqrt($sumNegSquares);
    }
    
    return [
        'positive' => $distPositive,
        'negative' => $distNegative
    ];
}

/**
 * Hitung skor preferensi (Closeness Coefficient)
 * **PERBAIKAN:** Sudah termasuk penanganan Division by Zero
 * @param array $distPositive - Jarak ke ideal positif
 * @param array $distNegative - Jarak ke ideal negatif
 * @return array - Skor preferensi
 */
function preferenceScore($distPositive, $distNegative) {
    $scores = [];
    
    foreach ($distPositive as $i => $dPos) {
        $dNeg = $distNegative[$i] ?? 0;
        
        $denominator = (float)$dPos + (float)$dNeg;

        // Cek Division by Zero (jika D_Pos + D_Neg = 0)
        if ($denominator == 0) {
            // Beri skor netral 0.5 karena D_Pos = D_Neg = 0 (alternatif identik dengan A+ dan A-)
            $scores[$i] = 0.5; 
        } else {
            $scores[$i] = (float)$dNeg / $denominator;
        }
    }
    
    return $scores;
}

/**
 * Format angka untuk tampilan
 * @param float $number - Angka
 * @param int $decimals - Jumlah desimal
 * @return string
 */
function formatNumber($number, $decimals = 4) {
    // Pastikan input adalah float/integer
    if (!is_numeric($number)) {
        return 'N/A';
    }
    return number_format((float)$number, $decimals, '.', '');
}

/**
 * Mendapatkan ranking berdasarkan skor
 * @param array $scores - Array skor [alternatif => skor]
 * @return array - Array terurut dengan key alternatif [alternatif => skor]
 */
function rankAlternatives($scores) {
    // arsort untuk mengurutkan array asosiatif berdasarkan nilai (skor) dari yang terbesar ke terkecil
    arsort($scores); 
    return $scores;
}

/**
 * Sanitize input dari form
 * @param mixed $data - Data input
 * @return mixed - Data yang sudah di-sanitize
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validasi input nilai (harus 1-5)
 * @param int $value - Nilai input
 * @return bool
 */
function isValidScore($value) {
    return is_numeric($value) && $value >= 1 && $value <= 5;
}