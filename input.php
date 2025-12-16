<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Minat Bakat ✨</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .question-card {
            border: 1px solid #eee;
            border-left: 5px solid #9333EA;
        }
    </style>
    
    <?php
    // Fungsi PHP untuk menghasilkan HTML Slider secara efisien
    function generateSliderHtml($id, $title, $desc, $emoji = '✨') {
        // ID digunakan sebagai name untuk form dan ID elemen
        return '
            <div class="question-card mb-4 p-3 rounded shadow-sm">
                <div class="d-flex align-items-start mb-3">
                    <span class="fs-2 me-3">' . $emoji . '</span>
                    <div>
                        <h5 class="fw-bold mb-1">' . $title . '</h5>
                        <p class="small text-muted mb-0">' . $desc . '</p>
                    </div>
                </div>
                
                <div class="slider-wrapper">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-semibold">👎 Tidak Cocok</small>
                        <span class="badge bg-gradient-purple px-3 py-2" id="value_' . $id . '">3</span>
                        <small class="text-muted fw-semibold">👍 Sangat Cocok</small>
                    </div>
                    
                    <div class="position-relative mb-2">
                        <input type="range" class="form-range" name="' . $id . '" id="' . $id . '" min="1" max="5" value="3" oninput="updateSlider(\'' . $id . '\')">
                    </div>
                    
                    <div class="text-center">
                        <div class="emoji-display mb-2 fs-1" id="emoji_' . $id . '">😐</div>
                    </div>
                </div>
            </div>
        ';
    }
    ?>

</head>
<body>
    <div class="container py-4">
        <div class="mb-4">
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-gradient-purple" id="progressBar" style="width: 33.33%"></div>
            </div>
            <p class="text-center text-muted mt-2 small" id="progressText">Bagian 1 dari 3</p>
        </div>

        <form action="hasil.php" method="POST" id="mainForm">
            
            <div class="form-step active" id="step1">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="badge bg-gradient-purple mb-2">Bagian 1 dari 3</span>
                            <h2 class="fw-bold">Minat & Bakat <span class="small"></span></h2>
                            <p class="text-muted">Seberapa cocok kamu dengan topik-topik detail di bawah ini? (Skor 1: Tidak Cocok, 5: Sangat Cocok)</p>
                        </div>
                        
                        <h4 class="mt-4 mb-3 fw-bold text-primary">Bidang Media & Visualization (MV) <span class="fs-4">🖼️</span></h4>
                        <?php 
                        echo generateSliderHtml('minat_mv_1', 'Menghitung', 'Menganalisis proporsi dan ukuran desain.', '🧮');
                        echo generateSliderHtml('minat_mv_2', 'Menggambar', 'Mengilustrasikan dan menentukan layout visual.', '🎨');
                        echo generateSliderHtml('minat_mv_3', 'Menulis', 'Menyusun dan merancang naskah.', '✍️');
                        echo generateSliderHtml('minat_mv_4', 'Membaca', 'Menganalisis karya tulis dan naskah.', '📖');
                        ?>

                        <h4 class="mt-4 mb-3 fw-bold text-success">Bidang Pemrograman <span class="fs-4">💻</span></h4>
                        <?php 
                        echo generateSliderHtml('minat_prog_1', 'Menghitung', 'Memahami cara kerja algoritma dengan berbagai macam kompleksitas.', '🧮');
                        echo generateSliderHtml('minat_prog_2', 'Menggambar', 'Menggambarkan alur kerja program.', '🎨');
                        echo generateSliderHtml('minat_prog_3', 'Menulis', 'Mendokumentasikan code program dalam bentuk naskah atau narasi.', '✍️');
                        echo generateSliderHtml('minat_prog_4', 'Membaca', 'Memahami code dengan baik sesuai alur.', '📖');
                        ?>

                        <h4 class="mt-4 mb-3 fw-bold text-warning">Bidang Sistem Cerdas (SC) <span class="fs-4">💡</span></h4>
                        <?php 
                        echo generateSliderHtml('minat_sc_1', 'Menghitung', 'Memahami operasi matriks dan logika matematika kompleks.', '🧮');
                        echo generateSliderHtml('minat_sc_2', 'Menggambar', 'Memvisualkan arsitektur model sistem.', '🎨');
                        echo generateSliderHtml('minat_sc_3', 'Menulis', 'Mendokumentasikan laporan dengan pesudocode.', '✍️');
                        echo generateSliderHtml('minat_sc_4', 'Membaca', 'Menganalisis hasil pelatihan model.', '📖');
                        ?>

                        <button type="button" class="btn btn-primary w-100 py-3 rounded-pill mt-4" onclick="nextStep(2)">
                            Lanjut ke Tema Skripsi →
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-step" id="step2">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="badge bg-gradient-purple mb-2">Bagian 2 dari 3</span>
                            <h2 class="fw-bold">Tema Skripsi <span class="small"></span></h2>
                            <p class="text-muted">Seberapa tertarik kamu dengan topik skripsi detail di bawah ini?</p>
                        </div>
                        
                        <h4 class="mt-4 mb-3 fw-bold text-primary">Tema Skripsi Media & Visualization (MV) <span class="fs-4">🖼️</span></h4>
                        <?php 
                        echo generateSliderHtml('skripsi_mv_1', 'Penerapan Algoritma', 'Algoritma pengolahan citra dan visualisasi object.', '🌐');
                        echo generateSliderHtml('skripsi_mv_2', 'Game', 'Desain karakter, animasi, dan tampilan grafis.', '🎮');
                        echo generateSliderHtml('skripsi_mv_3', 'Sistem Informasi', 'Desain UI.', '📱');
                        echo generateSliderHtml('skripsi_mv_4', 'Media Pembelajaran', 'Desain serta konten interaktif untuk seluruh kalangan dan usia.', '📚');
                        ?>

                        <h4 class="mt-4 mb-3 fw-bold text-success">Tema Skripsi Pemrograman <span class="fs-4">💻</span></h4>
                        <?php 
                        echo generateSliderHtml('skripsi_prog_1', 'Penerapan Algoritma', 'Agoritma komputasi dan aplikasi.', '🌐');
                        echo generateSliderHtml('skripsi_prog_2', 'Game', 'Logika permainan dan interaksi.', '🎮');
                        echo generateSliderHtml('skripsi_prog_3', 'Sistem Informasi', 'Pengembangan database, API, dan arsitektur aplikasi/sistem.', '📱');
                        echo generateSliderHtml('skripsi_prog_4', 'Media Pembelajaran', 'Pembangunan aplikasi edukatif.', '📚');
                        ?>

                        <h4 class="mt-4 mb-3 fw-bold text-warning">Tema Skripsi Sistem Cerdas (SC) <span class="fs-4">💡</span></h4>
                        <?php 
                        echo generateSliderHtml('skripsi_sc_1', 'Penerapan Algoritma', 'Algoritma prediksi, optimasi, dan machine learning(ML).', '🌐');
                        echo generateSliderHtml('skripsi_sc_2', 'Game', 'Karakter dengan AI seperti NPC dan enemy.', '🎮');
                        echo generateSliderHtml('skripsi_sc_3', 'Sistem Informasi', 'Sistem pendukung keputusan.', '📱');
                        echo generateSliderHtml('skripsi_sc_4', 'Media Pembelajaran', 'Pembangunan model AI berupa machine learning untuk pembelajaran.', '📚');
                        ?>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary py-2 rounded-pill" onclick="prevStep(1)">
                                ← Kembali
                            </button>
                            <button type="button" class="btn btn-primary py-3 rounded-pill" onclick="nextStep(3)">
                                Lanjut ke Pekerjaan →
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-step" id="step3">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="badge bg-gradient-purple mb-2">Bagian 3 dari 3</span>
                            <h2 class="fw-bold">Pekerjaan Masa Depan <span class="small"></span></h2>
                            <p class="text-muted">Seberapa cocok bidang kerja detail ini denganmu?</p>
                        </div>
                        
                        <h4 class="mt-4 mb-3 fw-bold text-primary">Karir di Bidang Media & Visualization (MV) <span class="fs-4">🖼️</span></h4>
                        <?php 
                        echo generateSliderHtml('pekerjaan_mv_1', 'Programmer', 'Membuat aplikasi dan animasi dari desain dengan visual interaktif.', '💻');
                        echo generateSliderHtml('pekerjaan_mv_2', 'Animator', 'Menganimasikan object atau karakter dengan enimasi dan efek visual.', '🎨');
                        echo generateSliderHtml('pekerjaan_mv_3', 'Wirausaha', 'Menciptakan bisnis kreatif seperti studio desain dan animasi, atau content creator.', '💼');
                        echo generateSliderHtml('pekerjaan_mv_4', 'Admin', 'Mengelola arsip desain, konten, dan komunikasi proyek.', '👔');
                        ?>

                        <h4 class="mt-4 mb-3 fw-bold text-success">Karir di Bidang Pemrograman <span class="fs-4">💻</span></h4>
                        <?php 
                        echo generateSliderHtml('pekerjaan_prog_1', 'Programmer', 'Mendesain, mengembangkan, dan memelihara aplikasi skala besar.', '💻');
                        echo generateSliderHtml('pekerjaan_prog_2', 'Animator', 'Membuat animasi berbasis kode seperti simulasi atau tampilan aplikasi interaktif.', '🎨');
                        echo generateSliderHtml('pekerjaan_prog_3', 'Wirausaha', 'Membangun Startup berbasis teknologi atau aplikasi digital.', '💼');
                        echo generateSliderHtml('pekerjaan_prog_4', 'Admin', 'Mengatur database, sistem pengguna, dan server.', '👔');
                        ?>

                        <h4 class="mt-4 mb-3 fw-bold text-warning">Karir di Bidang Sistem Cerdas <span class="fs-4">💡</span></h4>
                        <?php 
                        echo generateSliderHtml('pekerjaan_sc_1', 'Programmer', 'Merancang dan mengimplementasikan algoritma AI atau machine learning.', '💻');
                        echo generateSliderHtml('pekerjaan_sc_2', 'Animator', 'Membangun animasi adaptif dengan sistem AI.', '🎨');
                        echo generateSliderHtml('pekerjaan_sc_3', 'Wirausaha', 'Mengimplementasikan algoritma AI dalam mengambil keputusan untuk efisiensi bisnis dan otomasi proses.', '💼');
                        echo generateSliderHtml('pekerjaan_sc_4', 'Admin', 'Mengelola data pelatihan dan performa model.', '👔');
                        ?>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary py-2 rounded-pill" onclick="prevStep(2)">
                                ← Kembali
                            </button>
                            <button type="submit" class="btn btn-success py-3 rounded-pill fw-bold">
                                🎯 Hitung Rekomendasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const stepIds = ['step1', 'step2', 'step3'];

        function nextStep(step) {
            stepIds.forEach(id => {
                document.getElementById(id).classList.remove('active');
            });
            document.getElementById('step' + step).classList.add('active');

            const progress = (step - 1) * 33.33 + 33.33;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = 'Bagian ' + step + ' dari 3';
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        function prevStep(step) {
            nextStep(step);
        }

        // FUNGSI UTAMA: Mengubah Nilai Badge dan Emoji
        function updateSlider(sliderId) {
            const slider = document.getElementById(sliderId);
            const valueDisplay = document.getElementById('value_' + sliderId);
            const emojiDisplay = document.getElementById('emoji_' + sliderId);
            const value = parseInt(slider.value);

            if (valueDisplay) {
                valueDisplay.textContent = value;
            }

            let emoji = '';
            switch (value) {
                case 1:
                    emoji = '😠'; // Sangat Tidak Cocok/Tertarik
                    break;
                case 2:
                    emoji = '🙁'; // Tidak Cocok/Tertarik
                    break;
                case 3:
                    emoji = '😐'; // Netral/Biasa Saja
                    break;
                case 4:
                    emoji = '🙂'; // Cocok/Tertarik
                    break;
                case 5:
                    emoji = '🤩'; // Sangat Cocok/Tertarik
                    break;
                default:
                    emoji = '😐';
            }
            
            if (emojiDisplay) {
                emojiDisplay.textContent = emoji;
            }
        }
        
        // Initialize all sliders and emojis on page load
        document.addEventListener('DOMContentLoaded', () => {
            const sliders = document.querySelectorAll('input[type="range"]');
            sliders.forEach(slider => {
                updateSlider(slider.id);
            });
            document.getElementById('step1').classList.add('active');
        });
    </script>
</body>
</html>