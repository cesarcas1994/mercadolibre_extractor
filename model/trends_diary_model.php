<?php

/**
 * trends_diary_model
 */

//analisys::trends_search($category);

$start_time = microtime(true);

$DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";

$fecha = analysis::today($exact_time = true);

$day_dir = $fecha['year'] . "-" . $fecha['mon'] . "-" . $fecha['mday'];

var_dump($day_dir);

$local_file = 'categoriesMLM.json';

$local = file_get_contents("D:/mercadolibre_data/" . $local_file);

$obj = json_decode($local, $assoc=true);

//var_dump($obj);

//$dir_save = $DEFAULT_SAVE_DIR . "Trends" . "/"  . "trends" . "_" . $day_dir . "/" . "trends" . "_" . $day_dir . "_" . $obj_num[$key]["name"] . ".json"; 

//var_dump($dir_save);

$total_articles = count($obj);
$obj_num = array_values($obj);

/*foreach ($obj_num as $key => $value) {
	for ($j=1;  $j <= ceil($total_articles / $limit); $j++)
}

//Por aqui me quede dividir en paquetes de 50 o 100 valorar mejora en velocidad
//trabajar desde la nube

trends_search_array
*/
foreach ($obj_num as $key => $value) {
	var_dump($obj_num[$key]['id']);
	$trend_json = analysis::trends_search_array($obj_num[$key]['id']);

	$dir_save = $DEFAULT_SAVE_DIR . "/" . "Trends" . "/"  . "trends" . "_" . $day_dir . "/" . "trends" . "_" . $day_dir  . "_" . $obj_num[$key]['name'] . ".json"; 
	$meli -> save_json($trend_json, $dir_save);	
}

$end_time = microtime(true);
 echo "<br> Rounded runtime : get_seller_articles_total -> " . round($end_time - $start_time, 4) . " seconds";
 echo "<br>download speed : " . round($total_articles/($end_time_get_seller_articles_total - $start_time_get_seller_articles_total), 4) . " art/seconds";