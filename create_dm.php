<?php
// create_dm.php
// Jalankan sekali dari browser atau CLI untuk membuat akun Decision Maker (DM) yang ter-hash
// WARNING: Hapus atau lindungi file ini setelah digunakan.

include 'ahp/ahp_core.php';

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$dms = [
    // Contoh akun dengan role yang deskriptif
    ['email' => 'admin_sekolah@example.com', 'nama' => 'Admin Sekolah', 'password' => 'adminpass', 'dm_number' => 1, 'role_label' => 'Administrator'],
    ['email' => 'kepala_sekolah@example.com', 'nama' => 'Kepala Sekolah', 'password' => 'kepalapass', 'dm_number' => 2, 'role_label' => 'Kepala Sekolah'],
    ['email' => 'guru_pokok@example.com', 'nama' => 'Guru Pengampu', 'password' => 'gurupass', 'dm_number' => 3, 'role_label' => 'Guru Pengampu']
];

foreach ($dms as $dm) {
    // Cek jika sudah ada
    $check = $conn->prepare("SELECT id FROM decision_makers WHERE email = ?");
    $check->bind_param('s', $dm['email']);
    $check->execute();
    $res = $check->get_result();

    if ($res && $res->num_rows > 0) {
        echo "DM already exists: {$dm['email']}<br>";
        $check->close();
        continue;
    }
    $check->close();

    // Insert dengan password hash
    $hash = password_hash($dm['password'], PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO decision_makers (email, nama, password, dm_number, role_label) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param('sssis', $dm['email'], $dm['nama'], $hash, $dm['dm_number'], $dm['role_label']);

    if ($insert->execute()) {
        echo "Created DM: {$dm['email']} with password: {$dm['password']}<br>";
    } else {
        echo "Failed to create {$dm['email']}: " . htmlspecialchars($insert->error) . "<br>";
    }
    $insert->close();
}

echo "\nDone. Please delete or protect this file after use.";
