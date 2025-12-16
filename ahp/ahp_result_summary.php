<?php
// FILE: ahp_result_summary.php (Fixed: Replace ID 1 Logic)
session_start();

// Cek akses Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // header("Location: login.php"); 
    // exit();
}

include 'ahp_core.php';

// ============================================================
// 1. LOGIKA REGISTRASI DM BARU
// ============================================================
$message_regist = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_dm'])) {
    $nama_baru  = trim($_POST['nama']);
    $email_baru = trim($_POST['email']);
    $role_baru  = trim($_POST['role']);
    $pass_plain = trim($_POST['password']);

    if (!empty($nama_baru) && !empty($email_baru) && !empty($pass_plain)) {
        $cek = $conn->prepare("SELECT id FROM decision_makers WHERE email = ?");
        $cek->bind_param("s", $email_baru);
        $cek->execute(); 
        if ($cek->get_result()->num_rows > 0) {
            $message_regist = "<div class='alert-error'>❌ Email sudah terdaftar!</div>";
        } else {
            $hash = password_hash($pass_plain, PASSWORD_DEFAULT);
            $q_max = $conn->query("SELECT MAX(dm_number) as mx FROM decision_makers");
            $next_num = ($q_max->fetch_assoc()['mx'] ?? 0) + 1;
            
            $stmt = $conn->prepare("INSERT INTO decision_makers (email, nama, password, dm_number, role_label) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $email_baru, $nama_baru, $hash, $next_num, $role_baru);
            if ($stmt->execute()) $message_regist = "<div class='alert-success'>✅ User <b>$nama_baru</b> berhasil dibuat.</div>";
            else $message_regist = "<div class='alert-error'>❌ Gagal: " . $stmt->error . "</div>";
        }
    }
}

// ============================================================
// 2. LOGIKA SIMPAN KONSENSUS (REPLACE ID 1)
// ============================================================
$message_consensus = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_save_consensus'])) {
    
    // Daftar tabel dan level yang akan diproses
    $tasks = [
        'kriteria'     => ['pattern' => 'kriteria_dm_%', 'table' => 'ahp_weights_kriteria',     'size' => 3, 'admin_level_name' => 'kriteria_dm_1'],
        'minat_bakat'  => ['pattern' => 'minat_dm_%',    'table' => 'ahp_weights_minat_bakat',  'size' => 4, 'admin_level_name' => 'minat_dm_1'],
        'tema_skripsi' => ['pattern' => 'tema_dm_%',     'table' => 'ahp_weights_tema_skripsi', 'size' => 4, 'admin_level_name' => 'tema_dm_1'],
        'pekerjaan'    => ['pattern' => 'pekerjaan_dm_%','table' => 'ahp_weights_pekerjaan',   'size' => 4, 'admin_level_name' => 'pekerjaan_dm_1']
    ];

    $success_count = 0;

    foreach ($tasks as $key => $task) {
        // A. Hitung Matriks Gabungan (Geometric Mean)
        $gdss_matrix = calculate_gdss_matrix($task['pattern']);
        
        if ($gdss_matrix) {
            // B. Hitung Bobot AHP
            $ir = $IR_MAP[$task['size']] ?? 0.90;
            $res = calculate_ahp($gdss_matrix, $ir);
            
            $w = $res['weights'];
            $cr = $res['CR'];
            $is_consistent = ($cr <= 0.1) ? 1 : 0;
            
            // C. UPDATE DATA DI ID 1 (TABEL WEIGHTS)
            // Cek dulu baris ID 1 ada atau tidak?
            $check_id1 = $conn->query("SELECT id, input_matrix_id FROM {$task['table']} WHERE id = 1");
            
            $input_matrix_id_target = 0;

            // Variabel dinamis untuk kolom
            $suffix = substr($task['table'], 12); // misal 'kriteria'
            $col_cr = "cr_" . $suffix;
            $cols = array_keys($w);
            
            // Siapkan Values untuk Binding
            $bind_params = [];
            $bind_types = "";
            $set_part = "";

            foreach ($cols as $c) {
                $set_part .= "$c=?, ";
                $bind_types .= "d";
                $bind_params[] = $w[$c];
            }
            // Tambah CR dan Is_Consistent
            $set_part .= "$col_cr=?, is_consistent=?";
            $bind_types .= "di";
            $bind_params[] = $cr;
            $bind_params[] = $is_consistent;

            if ($check_id1 && $check_id1->num_rows > 0) {
                // --- KASUS 1: ID 1 SUDAH ADA (LAKUKAN UPDATE / REPLACE) ---
                $row_data = $check_id1->fetch_assoc();
                $input_matrix_id_target = $row_data['input_matrix_id'];
                
                // Query Update Weights
                $sql = "UPDATE {$task['table']} SET $set_part WHERE id = 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($bind_types, ...$bind_params);
                if ($stmt->execute()) {
                    $success_count++;
                }
                
                // Update juga Matriks Input-nya agar sinkron
                if ($input_matrix_id_target > 0) {
                    $json_matrix = json_encode($gdss_matrix);
                    $stmt_m = $conn->prepare("UPDATE ahp_input_matrices SET input_matrix = ?, last_updated = NOW() WHERE id = ?");
                    $stmt_m->bind_param("si", $json_matrix, $input_matrix_id_target);
                    $stmt_m->execute();
                }

            } else {
                // --- KASUS 2: ID 1 BELUM ADA (BUAT BARU TAPI PAKSA ID=1 JIKA BISA) ---
                // 1. Simpan matriks dulu
                $input_id = save_input_matrix($task['admin_level_name'], $gdss_matrix);
                
                // 2. Insert Weight
                // Kita coba insert manual dengan ID 1 jika auto increment mengizinkan, 
                // atau biarkan auto increment jika tidak bisa maksa.
                // Tapi user minta "replace id 1", jadi asumsi id 1 harusnya sudah ada.
                // Jika belum ada, kita insert normal.
                
                $col_names = implode(', ', $cols) . ", $col_cr, is_consistent, input_matrix_id";
                $placeholders = str_repeat('?, ', count($cols) + 2) . "?";
                
                $sql = "INSERT INTO {$task['table']} ($col_names) VALUES ($placeholders)";
                $bind_types .= "i";
                $bind_params[] = $input_id;

                $stmt = $conn->prepare($sql);
                $stmt->bind_param($bind_types, ...$bind_params);
                if ($stmt->execute()) {
                    $success_count++;
                }
            }
        }
    }
    
    if ($success_count > 0) {
        $message_consensus = "<div class='alert-success'>✅ <b>BERHASIL UPDATE ID 1!</b> Data Konsensus Gabungan telah menimpa data Admin (ID 1).</div>";
    } else {
        $message_consensus = "<div class='alert-error'>⚠️ Gagal menyimpan. Pastikan ada data input dari pakar lain.</div>";
    }
}

// Konfigurasi Level Tampilan
$levels_config = [
    'kriteria' => ['title'=>'Kriteria Utama', 'pattern'=>'kriteria_dm_%', 'ir'=>$IR_MAP[3]??0.58],
    'minat_bakat' => ['title'=>'Minat & Bakat', 'pattern'=>'minat_dm_%', 'ir'=>$IR_MAP[4]??0.90],
    'tema_skripsi' => ['title'=>'Tema Skripsi', 'pattern'=>'tema_dm_%', 'ir'=>$IR_MAP[4]??0.90],
    'pekerjaan' => ['title'=>'Pekerjaan', 'pattern'=>'pekerjaan_dm_%', 'ir'=>$IR_MAP[4]??0.90]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin GDSS</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f9; margin: 0; }
        .container { max-width: 1000px; margin: 30px auto; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .block-layout { border: 1px solid #e0e0e0; border-radius: 12px; overflow: hidden; margin-bottom: 30px; background: white; }
        .header-dash { background: linear-gradient(135deg, #6a05ad, #8a2be2); color: white; padding: 30px; text-align: center; border-radius: 12px; margin-bottom: 30px; }
        .btn-consensus { background: #ff9800; color: white; border: none; padding: 15px 30px; border-radius: 30px; font-weight: bold; font-size: 16px; cursor: pointer; display: block; width: 100%; margin: 20px 0; transition: 0.3s; box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3); }
        .btn-consensus:hover { background: #e68900; transform: translateY(-2px); }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .result-table { width: 100%; border-collapse: collapse; }
        .result-table th { background: #6a05ad; color: white; padding: 12px; }
        .result-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
        .form-control { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn-add { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>

<div style="background: #e6e6fa; padding: 30px; text-align: center; color: #4b0082;">
    <h1 style="margin:0;">Dashboard Admin GDSS</h1>
    <p>Manajemen Keputusan Kelompok (Group Decision Support System)</p>
</div>

<div class="container">
    
    <?= $message_regist ?>
    <?= $message_consensus ?>

    <div style="background: #fff3cd; padding: 20px; border-radius: 10px; border: 1px solid #ffeeba; margin-bottom: 30px;">
        <h3 style="margin-top:0; color:#856404;">⚠️ Update Data Admin (ID 1)</h3>
        <p style="color:#856404;">
            Klik tombol ini untuk menghitung rata-rata input dari semua pakar, 
            lalu <b>menimpa (replace) data ID 1</b> di database dengan hasil perhitungan tersebut.
        </p>
        <form method="POST">
            <button type="submit" name="btn_save_consensus" class="btn-consensus" onclick="return confirm('Data pada ID 1 akan ditimpa dengan hasil rata-rata pakar. Lanjutkan?')">
                💾 UPDATE DATA ID 1 DENGAN HASIL KONSENSUS
            </button>
        </form>
    </div>

    <div class="header-dash">
        <h2 style="margin:0;">Preview Hasil Gabungan (Live)</h2>
        <p>Geometric Mean dari seluruh input Decision Maker.</p>
    </div>

    <?php foreach ($levels_config as $key => $config): ?>
        <?php
        $gdss_matrix = calculate_gdss_matrix($config['pattern']);
        if ($gdss_matrix):
            $result = calculate_ahp($gdss_matrix, $config['ir']);
            $cr_color = ($result['CR'] <= 0.1) ? '#d4edda' : '#f8d7da';
        ?>
        <div class="block-layout">
            <div style="padding: 15px; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                <b><?= $config['title'] ?></b>
                <span style="font-size:12px; background:#ddd; padding:2px 8px; border-radius:10px;">Geometric Mean</span>
            </div>
            <div style="padding: 20px;">
                <table class="result-table">
                    <thead><tr><th>Kriteria</th><th>Bobot</th><th>%</th></tr></thead>
                    <tbody>
                        <?php foreach ($result['weights'] as $k => $v): ?>
                        <tr>
                            <td style="text-align:left; font-weight:600;"><?= ucwords(str_replace('_',' ',$k)) ?></td>
                            <td><?= number_format($v, 4) ?></td>
                            <td><?= number_format($v*100, 2) ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:10px; padding:10px; background:<?= $cr_color ?>; border-radius:5px; font-weight:bold;">
                    CR: <?= number_format($result['CR'], 4) ?> (<?= ($result['CR']<=0.1)?'KONSISTEN':'TIDAK KONSISTEN' ?>)
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="block-layout">
        <div style="padding: 15px; background: #4b0082; color: white;"><b>➕ Tambah User Pakar</b></div>
        <div style="padding: 20px;">
            <form method="POST">
                <input type="text" name="nama" placeholder="Nama Lengkap" class="form-control" required>
                <input type="email" name="email" placeholder="Email" class="form-control" required>
                <input type="text" name="role" placeholder="Jabatan" class="form-control" required>
                <input type="text" name="password" placeholder="Password" class="form-control" required>
                <button type="submit" name="btn_add_dm" class="btn-add">Buat User</button>
            </form>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="login.php" style="color: #666; text-decoration: none;">Logout</a>
    </div>

</div>
</body>
</html>