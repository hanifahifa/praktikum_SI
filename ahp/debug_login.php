<?php
// FILE: reset_hash.php (Jalankan SEKALI)

include 'ahp_core.php'; 

$target_email = 'kevin@gmail.com'; // GANTI dengan email yang terdaftar
$new_password_plain = 'password'; // GANTI dengan password BARU yang Anda inginkan (misal: admin123)

if ($conn->connect_error) {
    die("Koneksi Database Gagal!");
}

// 1. Hitung Hash Baru
$new_password_hash = password_hash($new_password_plain, PASSWORD_DEFAULT);

// 2. Query Update ke Database
// Query ini akan mengganti isi kolom password dari 'password' menjadi hash panjang
$sql = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ss", $new_password_hash, $target_email);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<h2>SUCCESS: Password berhasil direset!</h2>";
            echo "<p>Email yang diperbarui: <strong>{$target_email}</strong></p>";
            echo "<p>Password BARU yang akan digunakan untuk login: <strong>{$new_password_plain}</strong></p>";
            echo "<p>Hash baru di DB: <code>{$new_password_hash}</code></p>";
            echo "<p>Sekarang coba login menggunakan email dan password baru di <a href='login.php'>login.php</a>.</p>";
        } else {
            echo "<h2>GAGAL: Email tidak ditemukan.</h2>";
        }
    } else {
        echo "Error saat menjalankan update: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error saat menyiapkan query: " . $conn->error;
}
?>