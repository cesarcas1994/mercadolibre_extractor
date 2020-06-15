<?php

ini_set('memory_limit', '1024M'); // or you could use 1G

/*
//$category_id = "MLM7734"; //tarjetas de memoria
//$category_id = "MLM189975"; //estampillas
$category_id = "MLM6662"; //telefonos

$object = $meli -> get_articles($category_id);
$total_articles = $object["paging"]["total"];
var_dump($total_articles);
$meli -> get_ranking_item_groups($total_articles, $category_id,  $offset_group = 1000);
*/
//$meli -> groups_maker(15.0, 195.0, 2032);

// Pendiente arreglar porque funciona o no la corrida de este tema para comprobar que para menos categorias de menos de 1000 items todo esta bien, depues pasar a correr categorias de mas de 1000 elementos, importante revisar funcion de menos de 1000 items que corra en modo menos de 1000 y mas de 1000, obtener ranking de elemtos menos de 1000 y ademas marcaje de final de deteccion de elemtos en cada categoria 13 de enero 2020. 

//test translate

// 1000<teléfono Con Diseño Giratorio Retro Para Casa, uno categoria menos a 1000 y otra con 2200elemetnos
$item_1 = "MLM723500120";
$item_2 = "MLM675900723";
$item_3 = "MLM655685169";
$item_4 = "MLM693249363";
$item_5 = "MLM562479258"; //cafe serrano categoria 4000
$item_6 = "MLM678770782"; //pieza rara de carro.


$array_error = array(
0 => 'MLM614815598',
1 => 'MLM674598428',
2 => 'MLM675983577',
3 => 'MLM666164613',
4 => 'MLM666164391',
5 => 'MLM722625101' 
);

$array_null_why = array(
12 => 'MLM630751858', 
  13 => 'MLM634896669', 
  15 => 'MLM637933744', 
  22 => 'MLM610737805', 
  23 => 'MLM747613027', 
  28 => 'MLM743140304', 
  29 => 'MLM733687313', 
  32 => 'MLM643545980', 
  33 => 'MLM723123054', 
  34 => 'MLM723123148', 
  35 => 'MLM723123197', 
  36 => 'MLM723123235', 
  37 => 'MLM723123319', 
  40 => 'MLM602325381', 
  62 => 'MLM630751858', 
  63 => 'MLM634896669', 
  65 => 'MLM637933744', 
  72 => 'MLM610737805', 
  73 => 'MLM747613027', 
  78 => 'MLM743140304', 
  79 => 'MLM733687313', 
  82 => 'MLM643545980', 
  83 => 'MLM723123054', 
  84 => 'MLM723123148', 
  85 => 'MLM723123197', 
  86 => 'MLM723123235', 
  87 => 'MLM723123319', 
  90 => 'MLM602325381', 
  112 => 'MLM630751858', 
  113 => 'MLM634896669', 
  115 => 'MLM637933744', 
  122 => 'MLM610737805', 
  123 => 'MLM747613027', 
  128 => 'MLM743140304', 
  129 => 'MLM733687313', 
  132 => 'MLM643545980', 
  133 => 'MLM723123054', 
  134 => 'MLM723123148', 
  135 => 'MLM723123197',
  136 => 'MLM723123235', 
  137 => 'MLM723123319', 
  140 => 'MLM602325381' 
  );

$array_null_why2 = array(
10 =>  'MLM655864746', 
13 =>  'MLM649934044', 
15 =>  'MLM699177938', 
16 =>  'MLM740199025', 
17 =>  'MLM730271851' 
);

$array_international = array(
1 => "MLB1020718649"
);

//$object_array = $meli -> load_json("predictorMachineLearning/MLM4605_predictor.json");

$title_existing = $meli -> get_articles_total_v2("MLM4605", $total_articles = null, $limit = 50, $country_id = "MLM", $plus = null, $force_calculation_under_1000 = false);

var_dump("$title_existing before array_unique");
var_dump($title_existing);

$title_existing_output = array_unique($title_existing); //Remove duplicate values ​​from an array

var_dump("$title_existing after array_unique");
var_dump($title_existing_output);

$total_items_category = $meli -> get_full_article_array($title_existing);

var_dump(count($total_items_category));

foreach ($total_items_category as $key => $value) {
 	$title_existing_after[$key] = $total_items_category[$key]["id"];
 } 

var_dump($title_existing_after);



/*
var_dump(count($object_array));

foreach ($object_array as $key => $value) {
  $title_existing[$key] = $object_array[$key]["id"];      
}
*/
foreach ($title_existing_after as $key => $value) {
	if ($value == "MLM739753599")
	{
		var_dump("anterior a filtro MLM739753599 encontrado en posicion");
		var_dump($key);
	}
}

var_dump($title_existing_after[7844]);

var_dump("$title_existing before array_unique");
var_dump($title_existing_after);

$title_existing_output = array_unique($title_existing_after); //Remove duplicate values ​​from an array

var_dump("$title_existing after array_unique");
var_dump($title_existing_output);

//2 method

var_dump("2 method input");
var_dump($title_existing_after);

$res2 = array(); 
foreach($title_existing_after as $key=>$val) {    
    $res2[$val] = true; 
} 
$res2 = array_keys($res2); 

var_dump("2 method output");
var_dump($res2);

foreach ($res2 as $key => $value) {
	if ($value == "MLM739753599")
	{
		var_dump("MLM739753599 encontrado en posicion");
		var_dump($key);
	}
}

$array_to_compare_example = array($item_1, $item_2, $item_3, $item_4, $item_5, /*$item_6*/);

//$array_indexed_to_compare_example = array('0' => $item_1, '1' => $item_2, '2' => $item_3);

//get_ranking_item($item_id_array, $category_id, $limit = 50, $country_id = "MLM")


//$object_array = $meli -> get_full_article_array($array_null_why2);

//$ranking = $meli -> get_ranking_item($item_1, $limit = 50);
//var_dump($ranking);
//var_dump($object_array);

//$ranking_array = $meli -> get_ranking_item_array($object_array, $limit = 50, $country_id = "MLM");

//var_dump($ranking_array);

// Testeo items post creation.
/*
for ($j=1;  $j <= 10000; $j++)
{
$item_post = "MLM722114352";

$item_1 = "MLM723500120";
$item_2 = "MLM705013060";
$item_3 = "MLM661112361";
$item_4 = "MLM736199856";
$item_5 = "MLM647406455";

//var_dump($item_post);

var_dump("1");
$ranking = $meli -> get_ranking_item($item_1, $limit = 50);
var_dump($ranking);
var_dump("2");
$ranking = $meli -> get_ranking_item($item_2, $limit = 50);
var_dump($ranking);
var_dump("3");
$ranking = $meli -> get_ranking_item($item_3, $limit = 50);
var_dump($ranking);
var_dump("4");
$ranking = $meli -> get_ranking_item($item_4, $limit = 50);
var_dump($ranking);
var_dump("5");
$ranking = $meli -> get_ranking_item($item_5, $limit = 50);
var_dump($ranking);
var_dump("///////////////////proximo/////////////////");
sleep(10);
}	
*/
/**
 * all_items_category_model.php
 */

//$item_id_input = "MLM662357553";

//$other_example = "MLM683182140";

/*

function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

Usage:
$myvar = array(1,2,3);
console_log( $myvar ); // [1,2,3]

/*

$object_input = $meli -> get_full_article($item_id_input);

$category_id = $object_input['category_id'];

$category_input = $meli -> get_articles($category_id);

$total_articles = $category_input["paging"]["total"];
var_dump($total_articles);

$limit= 50;

//empezar por n precio en moneda nacional, despues if es mayor que 1000 items


for ($j=1;  $j <= ceil($total_articles / $limit); $j++)
     {
       $category_array[] = $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50; 
     }

var_dump($category_array)  ;  

$total_item_category = $meli -> get_articles_array($category_array); 



//This format extracts elements in parallel count = get_articles_total -> $ limit.

//me quede por aca valorar usar get_articles_total o crear propia funcion que sirva para buscar el elemento de entrada y devolver en que posicion cayó usando multiple request desde la llamada al get articles.

*/




