<?php

/**
 * data_extractor_model_2
 */

ini_set('memory_limit', '4095M'); // or you could use 4G

//  ** Input **  /

//  Pick min and max of the 1659 father categories in the case of mexico //

$min_father_category = 8;
$max_father_category = 8;

$start_time = microtime(true);

$categories_and_total_items = $meli -> comparison_model_prepare_to_getfeatures_v3($min_father_category, $max_father_category, $plus= null, $country_base = "MLM");

$meli -> get_items_features_unified_v3($min_father_category, $max_father_category, $categories_and_total_items["category_without_cousins"], $country_base = "MLM");

$end_time = microtime(true);

echo "<br> indirect call - data extractor moder - time rounded: " . round($end_time - $start_time, 6) . " seconds";
echo "<br>download speed : " . round($categories_and_total_items["total_items"]/($end_time - $start_time), 4) . " art/seconds";

//$father_category = analisis::get_father_position_list("MLM194325") $father_category["min_list"], $father_category["max_list"]
