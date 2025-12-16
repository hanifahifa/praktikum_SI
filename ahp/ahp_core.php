<?php
// FILE: ahp_core.php (FINAL & FULL LOGIC)

// --- KONFIGURASI DATABASE ---
define('DB_SERVER', 'localhost:3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Ganti sesuai konfigurasi Anda
define('DB_NAME', 'ahp_db');

// --- KONEKSI KE DATABASE ---
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Tentukan nilai Random Index (IR)
$IR_MAP = [
    1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 
    5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41,
    9 => 1.45, 10 => 1.49
];

// DEFINISI KRITERIA
$global_criteria = ['minat_bakat', 'tema_skripsi', 'pekerjaan'];
$minat_bakat_sub = ['menghitung', 'menggambar', 'menulis', 'membaca'];
$tema_skripsi_sub = ['algoritma', 'game', 'si', 'media_pembelajaran'];
$pekerjaan_sub = ['programmer', 'animator', 'wirausaha', 'admin'];

/**
 * Fungsi inti untuk menghitung Bobot Prioritas (Eigenvector), Lambda Max, CI, dan CR.
 */
function calculate_ahp($pairwise_matrix, $IR) {
    $criteria_names = array_keys($pairwise_matrix);
    $n = count($criteria_names);

    if ($n < 2) {
        // Jika cuma 1 kriteria, bobot 1, CR 0
        return [
            'weights' => array_fill_keys($criteria_names, 1.0),
            'lambda_max' => $n, 'CI' => 0, 'CR' => 0, 'n' => $n, 'IR' => 0,
            'input_matrix' => $pairwise_matrix
        ];
    }

    // 1. Hitung Jumlah Kolom
    $col_sums = array_fill_keys($criteria_names, 0);
    foreach ($criteria_names as $row) {
        foreach ($criteria_names as $col) {
            $col_sums[$col] += $pairwise_matrix[$row][$col];
        }
    }

    // 2. Normalisasi Matriks & Hitung Bobot (Rata-rata Baris)
    $weights = array_fill_keys($criteria_names, 0);
    foreach ($criteria_names as $row) {
        $row_sum = 0;
        foreach ($criteria_names as $col) {
            $normalized_val = $pairwise_matrix[$row][$col] / $col_sums[$col];
            $row_sum += $normalized_val;
        }
        $weights[$row] = $row_sum / $n;
    }

    // 3. Hitung Consistency Vector (Lambda Max)
    $lambda_max = 0;
    foreach ($criteria_names as $col) {
        $lambda_max += $col_sums[$col] * $weights[$col];
    }

    // 4. Hitung CI dan CR
    $CI = ($lambda_max - $n) / ($n - 1);
    $CR = ($IR > 0) ? ($CI / $IR) : 0;

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
 * Fungsi untuk menyimpan Matriks Input (Step A)
 * PERBAIKAN: Sekarang benar-benar menyimpan ke database (INSERT/UPDATE)
 */
function save_input_matrix($level_name, $input_matrix) {
    global $conn;
    $input_matrix_json = json_encode($input_matrix);
    
    // Cek apakah data untuk level_name ini sudah ada?
    $stmt_check = $conn->prepare("SELECT id FROM ahp_input_matrices WHERE level_name = ?");
    $stmt_check->bind_param("s", $level_name);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {
        // JIKA ADA: Lakukan UPDATE
        $existing_id = $row['id'];
        $stmt_update = $conn->prepare("UPDATE ahp_input_matrices SET input_matrix = ?, last_updated = NOW() WHERE id = ?");
        $stmt_update->bind_param("si", $input_matrix_json, $existing_id);
        if ($stmt_update->execute()) {
            return $existing_id;
        }
    } else {
        // JIKA TIDAK ADA: Lakukan INSERT
        $stmt_insert = $conn->prepare("INSERT INTO ahp_input_matrices (level_name, input_matrix, last_updated) VALUES (?, ?, NOW())");
        $stmt_insert->bind_param("ss", $level_name, $input_matrix_json);
        if ($stmt_insert->execute()) {
            return $stmt_insert->insert_id;
        }
    }

    return 0; // Gagal
}

/**
 * Fungsi pembantu untuk mengambil Matriks Input dari ahp_input_matrices
 */
function get_input_matrix_by_id($input_matrix_id) {
    global $conn;
    if (!$input_matrix_id) return [];
    
    $stmt = $conn->prepare("SELECT input_matrix FROM ahp_input_matrices WHERE id = ?");
    $stmt->bind_param("i", $input_matrix_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return json_decode($row['input_matrix'], true);
    }
    return [];
}

/**
 * Fungsi untuk mengambil hasil Bobot Prioritas dari database
 */
function get_ahp_weights($table_name) {
    global $conn, $IR_MAP;

    // Cek apakah tabel ada di database untuk mencegah error
    $check_table = $conn->query("SHOW TABLES LIKE '$table_name'");
    if($check_table->num_rows == 0) return null;

    $stmt = $conn->prepare("SELECT * FROM $table_name LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $weights = [];
        
        // Deteksi nama kolom CR secara dinamis
        $cr_field_name_suffix = substr($table_name, 12); 
        $cr_field = "cr_" . $cr_field_name_suffix;
        
        foreach ($row as $key => $value) {
            // Ambil kolom yang merupakan bobot (bukan metadata)
            if (!in_array($key, ['id', 'input_matrix_id', $cr_field, 'is_consistent', 'last_updated'])) {
                $weights[$key] = floatval($value);
            }
        }
        
        $n = count($weights);
        $IR = $IR_MAP[$n] ?? 1;
        $input_matrix = get_input_matrix_by_id($row['input_matrix_id']);

        $CR_db = isset($row[$cr_field]) ? floatval($row[$cr_field]) : 0;

        return [
            'weights' => $weights,
            'CR' => $CR_db,
            'n' => $n,
            'IR' => $IR,
            'pairwise' => $input_matrix
        ];
    }
    return null;
}

/**
 * Fungsi GDSS: Menghitung Geometric Mean dari Beberapa Matriks Input
 */
function calculate_gdss_matrix($pattern_level_name) {
    global $conn;
    
    // Ambil matriks yang sesuai pola (misal: 'kriteria_dm_%')
    $sql = "SELECT input_matrix FROM ahp_input_matrices WHERE level_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pattern_level_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $matrices = [];
    while ($row = $result->fetch_assoc()) {
        $data = json_decode($row['input_matrix'], true);
        if (is_array($data)) {
            $matrices[] = $data;
        }
    }
    
    $num_dm = count($matrices);
    if ($num_dm == 0) return null; 

    // Inisialisasi Matriks Gabungan
    $combined_matrix = [];
    $criteria_keys = array_keys($matrices[0]); 

    // Rumus Geometric Mean
    foreach ($criteria_keys as $row) {
        foreach ($criteria_keys as $col) {
            $product = 1;
            foreach ($matrices as $matrix) {
                $val = isset($matrix[$row][$col]) ? floatval($matrix[$row][$col]) : 1;
                $product *= $val;
            }
            $combined_matrix[$row][$col] = pow($product, 1 / $num_dm);
        }
    }
    
    return $combined_matrix;
}
?>