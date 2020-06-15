<?php

/**
 * data_extractor_model
 */

ini_set('memory_limit', '4095M'); // or you could use 4G

//  ** Input **  /

//$title_type = "Impercaucho Impermeabilizante 12 Años";
//$title_brand = "Terracota";
//$title_model = "19 Lts";
//$title_plus = "";

//Impercaucho Impermeabilizante 12 Años Terracota 19 Lts

//$title_type = "Croquetas Alimento Perro Raza Mediana Cachorro";
//$title_brand = "Nupec";
//$title_model = "20 Kg";
//$title_plus = "";

//Croquetas Alimento Perro Raza Mediana Cachorro 20 Kg Nupec

//$title_type = "Placa Metálica Con 2 Contactos Aterrizados";
//$title_brand = "Ad-4677";
//$title_model = "Adir";
//$title_plus = "";

//Placa Metálica Con 2 Contactos Aterrizados Ad-4677 Adir

//$title_type = "Rasuradora Eléctrica";
//$title_brand = "Qp2510/10";
//$title_model = "Repuesto Cuchilla Oneblade";
//$title_plus = "";

//Rasuradora Eléctrica Qp2510/10 + Repuesto Cuchilla Oneblade

//$title_type = "Soldadora Inversora Máquina";
//$title_brand = "Redbo";
//$title_model = "Mma-130";
//$title_plus = "";

//Soldadora Inversora Máquina 127 Amp Mma-130 Redbo
  
//$title_type = "Memoria Micro Sd";
//$title_brand = "Kingston";
//$title_model = "64 Gb Canvas Select";
//$title_plus = "";

//$title_type = "1 Guía Prepagada Día Siguiente";
//$title_brand = "Dhl";
//$title_model = "1kg";
//$title_plus = "Recolección Gratis";

//1 Guía Prepagada Día Siguiente Dhl 1kg + Recolección Gratis

//$title_type = "cafe";
//$title_brand = "serrano 500 gramos";
//$title_model = "";
//$title_plus = "";

//$title_search = "Ssd Kingston A400 Disco Duro Solido 480 Gb"; 

//$item_reference_id =
//$title_search = $title_type . " " . $title_brand . " " . $title_model . " " . $title_plus;
//$title_search = preg_replace('/\s\s+/', ' ', $title_search); //Remove the remaining blanks spaces
//$title_search = trim($title_search);
//var_dump($title_search);
//$image_link_search = "http://mlm-s1-p.mlstatic.com/757141-MLM31369084668_072019-O.jpg";
//$country_base = "MLM";

//  *  //

//$local_file = $meli -> comparison_model_prepare_to_getfeatures($title_type, $title_brand, $title_model, $title_plus, $plus = null, $force_calculation_under_1000 = true, $country_base);

//$local_file = "MLM189882_predictor";

//$meli -> get_items_features_unified($local_file, $title_search, $country_base);

//  *  // new method

$min_children_category = 0;
$max_children_category = 0;

$start_time = microtime(true);

$cousin_and_total_items = $meli -> comparison_model_prepare_to_getfeatures_v2($min_children_category, $max_children_category, $plus= null, $force_calculation_under_1000 = true, $country_base = "MLM");

$meli -> get_items_features_unified_v2($min_children_category, $max_children_category, $cousin_and_total_items["category_cousins"], $country_base = "MLM");

$end_time = microtime(true);

echo "<br> indirect call - data extractor moder - time rounded: " . round($end_time - $start_time, 6) . " seconds";
echo "<br>download speed : " . round($cousin_and_total_items["total_items"]/($end_time - $start_time), 4) . " art/seconds";
