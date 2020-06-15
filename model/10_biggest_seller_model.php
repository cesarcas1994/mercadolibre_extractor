<?php

/**
 * 10_biggest_seller_model
 */

//ini_set('memory_limit', '4095M'); // or you could use 1G

//objetivo 
//1 - pasar la busqueda por todas las categorias hijas.
//2 - tomar los 10 elementos primeros en el ranking
//3 - buscar para cada uno el vendedor organizarlo en un array [vendedor_id] = [todas las ventas historicas]
//4- sort por cantidad 
//5- guardar archivo con json encode local, subirlo a la nube en un blob /extras
//proyeccion 3000 categorias, 30000 items, 30000 vendedores

/* code of data extractor dont belong here   *//*

$children_categories = $meli -> get_all_children_categories_mexico_local();

var_dump($children_categories);
	

$category_array = array(
	"0" => $children_categories[0],
	"1" => $children_categories[1],
	"2" => $children_categories[2],
	"3" => $children_categories[3],
	"4" => $children_categories[4],
	"5" => $children_categories[5]

);

// objetivos para continuar
//1- organizar para que se escoja un minimo y maximo de las categorias hijas totales
//2- acomplarlo con el descargador de items antes de calcular las features.
//3- empezar a descargar data (antes de features)
//4- poner que calcule las features automaticamente de esas categorias bajadas(se puede poner un marcador que guarde las categorias para despues mandarlas directo a features)



var_dump($category_array);

$category_cousins = $meli -> get_cousin_categories_array($category_array, $country_base = "MLM");

var_dump($category_cousins);
*/

//me quede revisar que la funcion sea efectiva 25 de febrero


$children_categories = $meli -> get_major_father_categories($country_id = "MLM");

foreach ($children_categories as $key => $value) {
	$category_10items[$key] = $children_categories[$key] . "&limit=50";
}

$search_items = $meli -> get_articles_array_country($category_10items, $country_id = "MLM");

$step = 0;
foreach ($search_items as $search => $value) {
	foreach ($search_items[$search]["results"] as $item => $value) {
		$seller_id_array[$step] = $search_items[$search]["results"][$item]["seller"]["id"];
		$step = $step + 1;
	}	
}

var_dump(count($seller_id_array));

$seller_object = analysis::get_seller($seller_id_array);

foreach ($seller_object as $key => $value) {
  if ($seller_object[$key]["seller_reputation"]["transactions"]["completed"] === null) {
    $seller_object[$key]["seller_reputation"]["transactions"]["completed"] = 0; 
  }
   $seller_sold_quantity[$seller_id_array[$key]]= $seller_object[$key]["seller_reputation"]["transactions"]["completed"];
}

arsort($seller_sold_quantity);

var_dump($seller_sold_quantity);

$count = 0;
foreach ($seller_sold_quantity as $key => $value) {
	$seller_id_array[$count] = $key;	
	$count = $count + 1;
}

var_dump($seller_id_array);

$seller_object = analysis::get_seller($seller_id_array);

var_dump($seller_object);


