<?php

/**
 * cell_comparison_model
 */

$SAVE_ARTICLES_COMPARE_TO_BRAZIL = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Modeloprediccion/compare_to_brazil/";


$start_time = microtime(true);

$Celulares_y_Smartphones = "MLM1055"; //category sheet

// Download the Mexican data we want

$meli -> get_articles_total($Celulares_y_Smartphones . "&price=2000.0-4500.0" . "&ITEM_CONDITION=2230284", 50, $options_work_and_save = array("get_conversion" => true, "more_solds" => true));

/*  Organize according to these parameters:  

	$options_save = array(
		"more_solds" => true,
		"more_views" => true,
		"more_conversion" => true
	)
*/

$organize = analysis::sort_localdata ("wamp64/www/cursoPHP/mercadolibre_comparador/data/Modeloprediccion/MLM1055/datos.json", $analysis_options = array("more_conversion" => true));

//var_dump($organize);

// Add to the answer the comparison with similar elements of "BRAZIL"

for ($i=0; $i < 100; $i++){

	//Choose the first 100 elements in the ranking to compare with the data of Brazil
	//We pass filter to the title "remove colors, obvious words .." for search in the Brazilian market

	//Pendiente valorar opción comparar atributos del item como modelo a comparar para demostrar matcheo

	$title_to_search = $meli -> remove_words_and_lower($organize[$i]['title'], $words_to_remove = "cell");
	
	$brazil_search = analysis::custom_search($title_to_search, "&ITEM_CONDITION=2230284", $country = "MLB");

	foreach ($brazil_search['results'] as $key => $value) {
		 $array_to_compare_example[$key] = $brazil_search['results'][$key]['id'];
	}

    $array_match = $meli -> compare_items($organize[$i]['item_id'], $array_to_compare_example, $options_method_to_compare = array("compare_title" => "preg_match"));	
    // $array_match size can be an array from 0 to 50 values

    if ($array_match == null) { 
    	echo "No match found for " . $organize[$i]['title'];
    	continue;
    }
    else {
    	echo "Yes match was found !! for " . $organize[$i]['title'];
    }

    $organize[$i]['compare_item'] = array();

    foreach ($array_match as $key => $value) {

		$organize[$i]['compare_item'][$key] = array(

			"country_to compare" => "MLB-BRAZIL",
			"name_to_compare" => $array_match[$key]["title"],
			"item_id_MLB" => $array_match[$key]["id"],
			"solds" => $array_match[$key]["sold_quantity"], //total item sales without time restriction
			"views" => analysis::total_item_visions ($array_match[$key]["id"]), //total views of this item without time restriction
			"price" => ($array_match[$key]["price"]/4) * 19.14,  //item price carried from brazilian to mexican currency
			"kx_price" => (($array_match[$key]["price"]/4) * 19.14/$organize[$i]["price"])
		);

		$organize[$i]["kx_price_" . $key] = (($array_match[$key]["price"]/4) * 19.14/$organize[$i]["price"]);
    }	

    var_dump($organize[$i]);
} 

var_dump($organize);

$meli -> save_articles($Celulares_y_Smartphones, $organize, $SAVE_ARTICLES_COMPARE_TO_BRAZIL, $options_save = array("more_conversion" => true));


$organize = analysis::sort_localdata ("wamp64/www/cursoPHP/mercadolibre_comparador/data/Modeloprediccion/compare_to_brazil/MLM1055/datos_conversion.json", $analysis_options = array("more_conversion" => true));

var_dump($organize);

$end_time = microtime(true);
echo "<br>Rounded runtime: " . round($end_time - $start_time, 4) . " seconds";


