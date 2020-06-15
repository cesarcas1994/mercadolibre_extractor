<?php

/**
 *all_market_search_comparison_model
 */

ini_set('memory_limit', '4095M'); // or you could use 1G

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

$title_type = "Soldadora Inversora Máquina";
$title_brand = "Redbo";
$title_model = "Mma-130";
$title_plus = "";

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

//$title_search = "Ssd Kingston A400 Disco Duro Solido 480 Gb"; //$item_reference_id =
$title_search = $title_type . " " . $title_brand . " " . $title_model . " " . $title_plus;
$title_search = preg_replace('/\s\s+/', ' ', $title_search); //Remove the remaining blanks spaces
$title_search = trim($title_search);
var_dump($title_search);
$image_link_search = "http://mlm-s1-p.mlstatic.com/757141-MLM31369084668_072019-O.jpg";
$country_base = "MLM";

//  *  //

$start_time = microtime(true);

// download items object before get features 

///////////  items general competition ///////////////
/*
$result_general_category_items = $meli -> category_generalitems_search($title_type, $title_brand, $title_model, $title_plus, $plus = null, $force_calculation_under_1000 = true, $country_base);

//$result_local_category_items = $meli -> category_localitems_search($title_type, $title_brand, $title_model, $title_plus, $plus, $country_id = "MLM", $force_calculation_under_1000 = false);

//$result_general_category_items["MLM"] = $result_local_category_items;

$step = analysis::get_category_predictor($title_search, $country_base);
$save_category = $step["id"];

$DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
$dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . '_predictor.json';

var_dump("dir_save");
var_dump($dir_save);

$meli -> save_json($result_general_category_items, $dir_save);
*/
/*
foreach ($result_general_category_items as $country => $value) {
	var_dump("test first element");
	var_dump($result_general_category_items[$country][0]);
	var_dump("count");
	var_dump(count($result_general_category_items[$country]));

	foreach ($result_general_category_items[$country] as $key => $value) {
	 	$title_existing[$country][$key] = $result_general_category_items[$country][$key]["id"];
	} 

	var_dump("$title_existing before array_unique");
	var_dump($title_existing);

	$title_existing_output = array_unique($title_existing); //Remove duplicate values ​​from an array

	var_dump("$title_existing after array_unique");
	var_dump($title_existing_output);
}
*/
/*
$result_general_category_items = $meli -> load_json("predictorMachineLearning/MLM120103_predictor.json");

foreach ($result_general_category_items as $country_id => $value){
			
	if ($result_general_category_items[$country_id] == null) {
		continue;
	}
	else{

		$items_features_category_items_country = $meli -> get_items_features($result_general_category_items[$country_id], $features_to_obtain = null, $country_id);

	}

	//push an array of category_items, ex: A + AB + ABC ...
	if($items_features_category_items != null){
		foreach ($items_features_category_items_country as $key => $value) {
			array_push($items_features_category_items, $items_features_category_items_country[$key]);
		}
	}
	else{
		$items_features_category_items = $items_features_category_items_country;
	}
}
*/
/*
$items_features_category_items_country = $meli -> get_items_features($result_general_category_items["MLM"], $features_to_obtain = null, $country_id = "MLM");

$items_features_category_items = $items_features_category_items_country;
*/
//$items_features_competition = $items_features_category_items;

///////////  items general competition ///////////////

//$expansion_method == "total_items_competition"
//$expansion_method = "primary_results_items_competition"
/*
$result_general_competition = $meli -> custom_generalcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus = null, $return_object = true, $expansion_method = "primary_results_items_competition");

foreach ($result_general_competition as $country_id => $value) {
	if ($result_general_competition[$country_id] == null) {
		continue;
	}
	else{
		var_dump("items_features_competition country");
		var_dump($country_id);

			//equal name of items for $expansion_method = "primary_results_items_competition"
			foreach ($result_general_competition[$country_id] as $key => $value) {
				$result_general_competition[$country_id][$key]["title"] = $title_type . " " . $title_brand;
			}	

		$items_features_competition_country = $meli -> get_items_features($result_general_competition[$country_id], $features_to_obtain = null, $country_id);
	}

	//push an array of competition items, ex: A + AB + ABC ...
	if($items_features_competition != null){
		foreach ($items_features_competition_country as $key => $value) {
			array_push($items_features_competition, $items_features_competition_country[$key]);
		}
	}
	else{
		$items_features_competition = $items_features_competition_country;
	}
	
}

var_dump("primary_results_items_competition");
var_dump($items_features_competition);
*/
///////////  items competition only country base total articles match category///////////////
/*
$result_general_competition = $meli -> custom_localcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus = null, $country_id = "MLM", $return_object = true, $expansion_method = "total_items_competition_match_category");

$items_features_competition_total_articles = $meli -> get_items_features($result_general_competition, $features_to_obtain = null, $country_id = "MLM");

//var_dump($items_features_competition_total_articles);

if($items_features_competition != null){
	foreach ($items_features_competition_total_articles as $key => $value) {
		array_push($items_features_competition, $items_features_competition_total_articles[$key]);
	}
}
else{
	$items_features_competition = $items_features_competition_total_articles;
}
var_dump("total_items_competition_match_category");
var_dump($items_features_competition);
*/
///////////  items competition only country base total articles without match category///////////////

$result_general_competition = $meli -> custom_localcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus = null, $country_id = "MLM", $return_object = true, $expansion_method = "total_items_competition");

$items_features_competition_total_articles = $meli -> get_items_features($result_general_competition, $features_to_obtain = null, $country_id = "MLM");

//var_dump($items_features_competition_total_articles);

if($items_features_competition != null){
	foreach ($items_features_competition_total_articles as $key => $value) {
		array_push($items_features_competition, $items_features_competition_total_articles[$key]);
	}
}
else{
	$items_features_competition = $items_features_competition_total_articles;
}
var_dump("total_items_competition");
var_dump($items_features_competition);

/////////////////////////////  old code //////////////////////////////////////////////////////////

///////////  items general match  Incomplete! ///////////////

/*

$result_match = $meli -> custom_generalmatch_search($title_search, $plus = null, $return_object = true);
//var_dump($result_match);

foreach ($result_match as $country_id => $value) {
	foreach ($result_match[$country_id] as $item => $value) {
		$result_match[$country_id][$item]["title"] = $title_search;
	}
}

for
*/

///////////  items match ///////////////
/*
$result_match = $meli -> custom_generalmatch_search($title_search, $plus = null, $return_object = true);
var_dump($result_match);

//Fix title to match

foreach ($result_match as $country_id => $value) {
	foreach ($result_match[$country_id] as $item => $value) {
		$result_match[$country_id][$item]["title"] = $title_search;
	}
}


//var_dump($result_match["MLM"]);

$items_features_match = $meli -> get_items_features($result_match["MLM"], $features_to_obtain = null, $country_id = "MLM");
var_dump($items_features_match); 
*/



///////////  items competition ///////////////

//Pendiente arreglar la cantidad de items de competicion como la busqueda por "primary_results"
/*
$result_competition = $meli -> custom_localcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus = null, $country_id = "MLM", $return_object = true, $expansion_method = "primary_results_items_competition");

//var_dump("result_competition");
//var_dump($result_competition[0]);


$items_features_competition = $meli -> get_items_features($result_competition, $features_to_obtain = null, $country_id = "MLM");
*/

/////////////////////////////  Finish old code! ///////////////////////////////////////////////////


// union match + competition

var_dump("sizeof($items_features_match)");
var_dump(sizeof($items_features_match));

if($items_features_match != null){
	foreach ($items_features_competition as $key => $value) {
		array_push($items_features_match, $items_features_competition[$key]);
	}
}
else{
	$items_features_match = $items_features_competition;
}

//var_dump("sizeof($items_features_match)");
//var_dump(sizeof($items_features_match));

// push to blob storage

// Post to azure blob
	
	// 1-option deprecated 
	//$Azure_csvtoAzureBlob = "https://predictmarket.azurewebsites.net/api/csvtoAzureBlob?code=Pg/fHaGClE2oIq5u7OY38eXabo8L0aezCYJ5fhTjYuGQPmI3Rc03aA==";

	// 2- option activated
	$Azure_csvtoAzureBlob = "https://mlconexionmeli.azurewebsites.net/api/csvtoAzureBlob?code=xMNGNalSwHfaSpdkyfD/5lcvqftl8wBAfl7PKtX5qrQbyUsYmSqasQ==";

	$Azure_logicApps_TrainingMachineLearning = "https://prod-19.centralus.logic.azure.com:443/workflows/96e3d6f34edc43be849ae7304c8fedf7/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=9rcOZAumI5eJy7kJEIK7ylJPIUkxN9LNNRnJAnogYZU";

	$step = analysis::get_category_predictor($title_search, $country_base);
	//$match_category = $step["id"];
	$match_category = "table";
	
	$body = array(
		"items" => $items_features_match,
		"category_id" => $match_category
	);
	
	$save_category = $step["id"];

	$DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
	$dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . 'features_predictor.json';

	var_dump("dir_save");
	var_dump($dir_save);

	$meli -> save_json($body, $dir_save);
	
	//load features archive
	
	//$body = $meli -> load_json("predictorMachineLearning/MLM1077features_predictor.json");

	var_dump("antes de json_encode");
	var_dump($body);
	//var_dump(typeof($body));

	$body = json_encode($body);

	switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }

	var_dump("despues de json_encode");
	var_dump($body);

	$CURL_OPTS = array(
		//revisar si es necesario esta linea
	    //CURLOPT_USERAGENT => "MELI-PHP-SDK-2.0.0", 
	    // revisar si es necesario que este en true 
	    //CURLOPT_SSL_VERIFYPEER => true,
	    CURLOPT_SSL_VERIFYPEER => false,
	    //CURLOPT_CONNECTTIMEOUT => 10, 
	    CURLOPT_CONNECTTIMEOUT => 0, 
	    CURLOPT_RETURNTRANSFER => 1, 
	    CURLOPT_TIMEOUT => 300
	);

	$opts = array(
	    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
	    CURLOPT_POST => true, 
	    CURLOPT_POSTFIELDS => $body
	);

	$uri = $Azure_csvtoAzureBlob;

	$ch = curl_init($uri);
	curl_setopt_array($ch, $CURL_OPTS);
	curl_setopt_array($ch, $opts);

	$return["body"] = json_decode(curl_exec($ch), true);
	$return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);	

	curl_close($ch);

	var_dump($return);

// method new for all countrys
/*
foreach ($result as $key => $value) {

	echo "Pais analizado: " . $key;
	$items_features = $meli -> get_items_features($result[$key], $features_to_obtain = null, $country_id = $key);
	var_dump($items_features);
}
*/


/* method old

foreach ($result as $key => $value) {

	echo "Pais analizado: " . $key;
	$conversion_array = $meli -> get_conversion_array($result[$key], "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");

}

*/
//until here 5 sec
// organize by pictures distance 
//Pendiente Valorar crear una funcion para buscar en array de objetos url , precios, titles, fechas de creacion etc.
//Pendiente cuando pasas a Azure cloud se puede usar una estructura donde hasta aqui trabaje guarde en table storage y a partir de este comentario lo que haga es llamar tipo request por lo que se ahorraria realizar el match siempre al principio y despues vedria el adorno de la inofrmacon que retorna.

//Pendiente mucho tiempo 80 seg

/*

$count = 0;
foreach ($result as $country_name => $value) {
            
	foreach ($result[$country_name] as $item_id => $value) {

		//var_dump($result[$country_name][$item_id]);

		$pictures_to_search[$country_name][$item_id] = $result[$country_name][$item_id]["pictures"][0]["url"];
		$count = $count + 1;
	}

	//$picture_comparation = analysis::picture_comparation($image_link_search, $pictures_to_search[$country_name], $sort_distance = true);
	//var_dump($picture_comparation);

}
*/
$end_time = microtime(true);

echo "<br> indirect call - all_market_search_comparison_model- time rounded: " . round($end_time - $start_time, 6) . " seconds";

/*

$name_and_coin = array(
        "MLA" => "ARS", // Argentina 
        "MLB" => "BRL", // Brasil
        "MCO" => "COP", // Colombia
        "MCR" => "CRC", // Costa Rica
        "MEC" => "USD", // Ecuador
        "MLC" => "CLP", // Chile
        "MLM" => "MXN", // Mexico
        "MLU" => "UYU", // Uruguay
        //"MLV" => "", // Venezuela need another api
        "MPA" => "PAB", // Panama
        "MPE" => "PEN", // Peru
        "MPT" => "EUR", // Prtugal
        "MRD" => "DOP"  // Dominicana
    );

$currency_exchange_array = analysis::get_today_currency_exchange("MXN", $name_and_coin);

var_dump($currency_exchange_array);

$start_time = microtime(true);

// 6/12/12 Pendiente despues de haber testeado y arreglado problemas, ej quitar warning, quitar vadumps innecesarios, pasar a intoducir elementos de Ibushak.

//Esta idea puede testearse con cualquier nombre de busqueda(entrada), es muy poderosa!

//Pendiente valorar crear una funcion guarde los ids para buscarlos al final de todos los países , meta superar 8 seg de carga. Valorar que informacio hace falta de esos items.

//Pendiente valorar introducir lista de elementos 

// 19/12/12 Pendiente necesario pasar a quitar todos los intermediarios y solamente retornar al final objetos de los items que hacen match, con 1) objeto o id (valorar rapidez) 2) precio mex, objetivo disminuir al max el tiempo de reproduccion, valorar pasar a solo una funcion.

$number_match = 0 ;

foreach ($name_and_coin	as $name => $coin) {

	var_dump("Country search : " . $name);

	$country_search = analysis::custom_search($search, $plus = null, $country = $name, $pass_access_token = false);

	foreach ($country_search['results'] as $key => $value) {

	    $titles_array_to_compare[$key] = $country_search['results'][$key]['title'];
	}

	var_dump($titles_array_to_compare);

	$position_match_array = $meli -> compare_title_preg_match_title_input($search, $titles_array_to_compare, $option_match = array("return_position_match" => true) , $remove_words_relatedto = null);

	var_dump($position_match_array);

	foreach ($position_match_array as $key => $value) {

		if ($value) {

			var_dump($key);
			$match_item = $country_search['results'][$key];

			var_dump($match_item["price"]);
			var_dump($currency_exchange_array[$name]);

			$all_matches_prices[$number_match] = $match_item["price"] * $currency_exchange_array[$name];

			var_dump("Price in MXM is " . $match_item["price"] * $currency_exchange_array[$name]);
			var_dump($match_item);

			$all_matches_ids[$number_match] = $country_search['results'][$key]['id'];
			$all_matches_bodys[$number_match] = $country_search['results'][$key];

			$number_match = $number_match + 1;
		}	
	}

}

//// print all results (series mod) //

$azure_count = 0;

foreach ($all_matches_bodys as $key => $value) {

	switch ($all_matches_bodys[$key]['sold_quantity']) {
		case 1:
			$all_matches_quantity_solds[$key] = $all_matches_bodys[$key]['sold_quantity'];
			$search_it_scraping[$key] = false; // No necessary to call because you already have it
			break;
		case 2:
			$all_matches_quantity_solds[$key] = $all_matches_bodys[$key]['sold_quantity'];
			$search_it_scraping[$key] = false;
			break;	
		case 3:
			$all_matches_quantity_solds[$key] = $all_matches_bodys[$key]['sold_quantity'];
			$search_it_scraping[$key] = false;
			break;
		case 4:
			$all_matches_quantity_solds[$key] = $all_matches_bodys[$key]['sold_quantity'];
			$search_it_scraping[$key] = false;
			break;	
		default:
			$permalinks_array[$azure_count] = $all_matches_bodys[$key]["permalink"]; //save data to scraping
			$all_matches_quantity_solds[$key] = 0; //temporaly zero
			$azure_count = $azure_count + 1;
			$search_it_scraping[$key] = true;
			break;
	}
}

//primero testear

var_dump($all_matches_quantity_solds);

//var_dump($search_it_scraping);

//var_dump($permalinks_array);

$url_azure_cloud = "https://getsoldquantitymeliv2.azurewebsites.net/api/HttpTrigger1?code=7sRuvOljlYYN9UaILS79qEN2G1nqaCs5pSMAdZS2Bcd8wB1ooFxBZg==";

$sold_quantity_array_scraping =  $meli -> get_sold_quantity_array($permalinks_array, $url_azure_cloud);

$azure_count = 0;

foreach ($all_matches_bodys as $key => $value) {
	
	if ($search_it_scraping[$key] == true)
	{
		$all_matches_quantity_solds[$key] = $sold_quantity_array_scraping[$azure_count];
		$azure_count = $azure_count + 1;
	}		
}

var_dump($all_matches_quantity_solds);




foreach ($all_matches_bodys as $key => $value) {
	
	//title

	 var_dump("title : " . $all_matches_bodys[$key]["title"]); 

	//price

	 var_dump("prices : " . $all_matches_prices[$key]);

	 //sold_quantity

	 var_dump("sold_quantity : " . $all_matches_quantity_solds[$key]);

	 var_dump("/////  **  END ** //////");
}

var_dump($all_matches_ids);

$end_time = microtime(true);
echo "<br> Rounded runtime : all_market_search_comparison_model -> " . round($end_time - $start_time, 4) . " seconds";

//Create function union

/**
 * 
 */
