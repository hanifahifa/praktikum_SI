<?php
/**
 * BOBOT AHP STATIS
 * Data diambil dari perhitungan Excel yang sudah final
 */

// ========================================
// BOBOT KRITERIA MINAT BAKAT
// ========================================
$ahp_minat_bakat = [
    'Menghitung' => 0.5548335979,
    'Menggambar' => 0.2373046562,
    'Menulis' => 0.1263239211,
    'Membaca' => 0.08153782483
];

// ========================================
// BOBOT KRITERIA TEMA SKRIPSI
// ========================================
$ahp_tema_skripsi = [
    'Penerapan Algoritma' => 0.490039141,
    'Game' => 0.2583741749,
    'SI' => 0.1527477023,
    'Media Pembelajaran' => 0.08331699263
];

// ========================================
// BOBOT KRITERIA PEKERJAAN
// ========================================
$ahp_pekerjaan = [
    'Programmer' => 0.5582890862,
    'Animator' => 0.293503854,
    'Wirausaha' => 0.1440458047,
    'Admin' => 0.06931652544
];

// ========================================
// BOBOT GLOBAL (Level 1)
// ========================================
$ahp_global = [
    'Minat Bakat' => 0.5905562225,
    'Tema Skripsi' => 0.2507536348,
    'Pekerjaan' => 0.1586901427
];

// ========================================
// DATA AHP LENGKAP UNTUK DETAIL
// ========================================
$ahp_detail = [
    'minat_bakat' => [
        'weights' => $ahp_minat_bakat,
        'lambda_max' => 4.171964832,
        'CI' => 0.05732161062,
        'CR' => 0.06369067846,
        'pairwise' => [
            'Menghitung' => [1, 4, 4, 5],
            'Menggambar' => [0.25, 1, 3, 3],
            'Menulis' => [0.25, 0.333, 1, 2],
            'Membaca' => [0.2, 0.333, 0.5, 1]
        ]
    ],
    'tema_skripsi' => [
        'weights' => $ahp_tema_skripsi,
        'lambda_max' => 4.113303238,
        'CI' => 0.03776774616,
        'CR' => 0.0419641624,
        'pairwise' => [
            'Penerapan Algoritma' => [1, 3.162, 3.976, 3.807],
            'Game' => [0.316, 1, 3, 3.5],
            'SI' => [0.251, 0.33, 1, 3.162],
            'Media Pembelajaran' => [0.263, 0.286, 0.316, 1]
        ]
    ],
    'pekerjaan' => [
        'weights' => $ahp_pekerjaan,
        'lambda_max' => 4.084067026,
        'CI' => 0.02802234211,
        'CR' => 0.03113593567,
        'pairwise' => [
            'Programmer' => [1, 3.409, 4.865, 5.18],
            'Animator' => [0.293, 1, 3.08, 5.091],
            'Wirausaha' => [0.206, 0.325, 1, 3.08],
            'Admin' => [0.193, 0.196, 0.325, 1]
        ]
    ],
    'global' => [
        'weights' => $ahp_global,
        'lambda_max' => 3.03649701,
        'CI' => 0.01824850488,
        'CR' => 0.03146293945
    ]
];