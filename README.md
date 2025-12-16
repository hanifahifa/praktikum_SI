GDSS (AHP–TOPSIS) Web — README

Deskripsi
---------
Project ini adalah aplikasi SPK berbasis AHP + TOPSIS yang telah dimodifikasi menjadi GDSS (Group Decision Support System) dengan konsep Decision Makers (DM) yang dipisahkan dari akun admin.

Fitur utama
- AHP computation (weights stored in DB)
- DM accounts (table `decision_makers`) dengan label peran (Administrator, Kepala Sekolah, Guru, dll)
- Admin account di tabel `users`
- DM dashboard dan view bobot AHP

Persyaratan
- PHP 7.4+ atau 8.x
- MySQL / MariaDB
- Web server (Laragon / XAMPP / Apache)

Persiapan & Menjalankan
-----------------------
1. Letakkan folder `SI` di folder webserver Anda (mis: `C:\laragon\www\SI`).
2. Import database:

   - Gunakan phpMyAdmin atau mysql CLI untuk mengimpor file `ahp_db.sql`.

   Contoh (CLI):

```sql
-- dari Windows CMD
mysql -u root -p < path\to\ahp_db.sql
```

3. Sesuaikan konfigurasi database di `ahp/ahp_core.php` jika diperlukan (DB server, user, password, nama DB):

```php
// ahp/ahp_core.php
define('DB_SERVER', 'localhost:3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ahp_db');
```

4. Buat akun Decision Makers contoh (jalankan sekali):
   - Buka di browser: http://localhost/SI/create_dm.php
   - Skrip akan menambahkan contoh akun berikut (jika belum ada):
     - admin_sekolah@example.com / adminpass (Administrator)
     - kepala_sekolah@example.com / kepalapass (Kepala Sekolah)
     - guru_pokok@example.com / gurupass (Guru Pengampu)
   - Catat password yang ditampilkan, lalu segera hapus atau pindahkan file `create_dm.php` setelah digunakan.

5. Akses aplikasi:
   - Untuk keamanan, index.php sekarang diarahkan ke halaman login.
   - Buka: http://localhost/SI -> akan diarahkan ke http://localhost/SI/ahp/login.php

6. Login:
   - Admin: gunakan akun dari tabel `users` (jika sudah ada). Contoh ada akun `kevin@gmail.com` (password di-hash — gunakan `debug_login.php` untuk mereset jika perlu).
   - DM: gunakan akun yang dibuat via `create_dm.php`.

Melihat Bobot AHP dan Matriks
-----------------------------
Anda dapat melihat bobot yang tersimpan dan matriks pairwise dengan membuka:
- http://localhost/SI/ahp/ahp_weights.php?level=kriteria
- http://localhost/SI/ahp/ahp_weights.php?level=minat_bakat
- http://localhost/SI/ahp/ahp_weights.php?level=tema_skripsi
- http://localhost/SI/ahp/ahp_weights.php?level=pekerjaan

Keamanan & Tips
---------------
- HAPUS atau amankan `create_dm.php` setelah digunakan.
- Jangan letakkan file yang berisi password plaintext di server publik.
- Gunakan HTTPS pada server produksi.
- Untuk menyesuaikan peran DM, edit kolom `role_label` di tabel `decision_makers`.

Mengembalikan Beranda (opsional)
--------------------------------
Jika Anda ingin kembali menampilkan beranda publik (index original), restore file `index.php` dari repositori/backup atau ganti redirect yang ada.

Bantuan / Debugging
-------------------
- Jika ada error koneksi DB, periksa `ahp/ahp_core.php` dan pastikan MySQL berjalan.
- Untuk mereset password user admin, gunakan `ahp/debug_login.php` (jalankan dengan parameter yang ada di file).

Catatan pengembang
------------------
- Perubahan utama terkait GDSS:
  - Tabel `decision_makers` ditambahkan dengan kolom `role_label`.
  - Login sekarang memeriksa tabel `users` (admin) terlebih dahulu lalu `decision_makers`.
  - DM memiliki halaman `dm_dashboard.php`.

Jika Anda ingin saya membuat UI admin read-only untuk melihat daftar DMs atau menambahkan workflow agregasi AHP untuk multi-DM, beri tahu saya dan saya akan lanjutkan.
