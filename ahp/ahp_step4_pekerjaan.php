<?php
// FILE: ahp_step4_pekerjaan.php
include 'ahp_core.php';
include 'template_ahp.php';

$level_name = 'pekerjaan';
$table_name = 'ahp_weights_pekerjaan';
$title = 'Bobot Subkriteria Pekerjaan (Level 2)';
$criteria = $pekerjaan_sub;
$prefix = 'pekerjaan_';
$step = 4;
$matrix_size = 4;
$IR = $IR_MAP[$matrix_size];

$result = get_ahp_weights($table_name); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matrix = process_input($prefix, $criteria);
    $result_new = calculate_ahp($matrix, $IR);
    
    // 1. Simpan Matriks Input
    $input_id = save_input_matrix($level_name, $matrix);

    // 2. Simpan Bobot Prioritas
    save_ahp_weights($table_name, $input_id, $result_new);
    $result = $result_new; 
}

render_template_start("Step {$step}: {$title}", "Matriks {$matrix_size}x{$matrix_size} (IR: {$IR})");
render_ahp_content($title, $prefix, $criteria, $result, $step);
render_template_end();
?>