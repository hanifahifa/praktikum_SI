<?php
// FILE: login.php
session_start();
include 'ahp_core.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password_plain = $_POST['password'] ?? '';

    if (empty($email) || empty($password_plain)) {
        $error_message = "Email dan Password harus diisi.";
    } else {
        $login_success = false;

        // ---------------------------------------------------------
        // 1. CEK TABEL ADMIN (users)
        // ---------------------------------------------------------
        $stmt = $conn->prepare("SELECT email, nama, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {
            // Verifikasi Password Admin
            if (password_verify($password_plain, $user['password'])) {
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nama']  = $user['nama'];
                $_SESSION['role']       = 'admin';
                
                header("Location: ahp_result_summary.php"); // Admin ke Dashboard
                exit();
            }
        }
        $stmt->close();

        // ---------------------------------------------------------
        // 2. CEK TABEL DECISION MAKER (decision_makers)
        // (Hanya jika belum berhasil login sebagai admin)
        // ---------------------------------------------------------
        if (!$login_success) {
            $stmt2 = $conn->prepare("SELECT id, email, nama, password, dm_number, role_label FROM decision_makers WHERE email = ?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $res2 = $stmt2->get_result();

            if ($dm = $res2->fetch_assoc()) {
                // Verifikasi Password DM
                if (password_verify($password_plain, $dm['password'])) {
                    $_SESSION['user_email']     = $dm['email'];
                    $_SESSION['user_nama']      = $dm['nama'];
                    $_SESSION['role']           = 'dm'; 
                    $_SESSION['dm_id']          = $dm['id'];
                    $_SESSION['dm_number']      = $dm['dm_number'];
                    $_SESSION['dm_role_label']  = $dm['role_label'];

                    // BERHASIL: Redirect ke Halaman Input Khusus DM
                    header("Location: ahp_dm_kriteria_summary.php");
                    exit();
                }
            }
            $stmt2->close();
        }

        // Jika sampai sini belum exit, berarti gagal di kedua tabel
        $error_message = "Email tidak ditemukan atau Password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem AHP</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f0f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-container { width: 100%; max-width: 400px; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 0 40px rgba(106, 5, 173, 0.15); border-top: 5px solid #6a05ad; }
        .header h1 { margin: 0; font-size: 28px; color: #4b0082; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4b0082; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .login-btn { width: 100%; padding: 14px; background: #6a05ad; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .login-btn:hover { background: #4b0082; }
        .error-box { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #f5c6cb; }
        .helper-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .helper-link a { color: #6a05ad; text-decoration: none; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="header"><h1>Login AHP</h1><p style="text-align:center;color:#888;">Masuk sebagai Admin atau Pakar</p></div>
    
    <?php if ($error_message): ?>
        <div class="error-box"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button type="submit" class="login-btn">Masuk</button>
    </form>
    
    <div class="helper-link">
        <p>Lupa password atau data manual?<br><a href="bantuan_password.php">Buat Password Hash Baru</a></p>
    </div>
</div>
</body>
</html>