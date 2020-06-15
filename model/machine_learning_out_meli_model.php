<?php

/**
 * machine_learning_out_meli_model
 */

$min_children_category = 0;
$max_children_category = 0;

$start_time = microtime(true);

//$children_category_array = array('0' => "MLM1417");

//$test_answer = $meli -> get_blob_saves_children_categories_data($children_category_array);
//var_dump($test_answer);

 $meli -> comparison_model_machine_learning_out_meli($min_children_category, $max_children_category,  $country_base = "MLM");

$end_time = microtime(true);

echo "<br> indirect call - machine_learning_out_meli_model - time rounded: " . round($end_time - $start_time, 6) . " seconds";