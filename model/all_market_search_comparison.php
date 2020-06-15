<?php

/**
 *all_market_search_comparison_model
 */

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

$currency_exchange_array = analysis::get_today_currency_exchange("MXN", $Name_coin);


$start_time = microtime(true);

//  *  7/
$search = "café cubano serrano"; //$item_reference_id = 
//  *  //



foreach ($name_and_coin	as $name => $coin) {

	$country_search = custom_search($search, $plus = null, $country = $name, $pass_access_token = false);



	foreach ($country_search['results'] as $key => $value) {

	    $titles_array_to_compare[$key] = $country_search['results'][$key]['title'];
	}

	$position_match_array = $meli -> compare_title_preg_match_title_input($search, $titles_array_to_compare, $option_match = array("return_position_match" => true) , $remove_words_relatedto = null);

	foreach ($position_match_array as $key) {

		if ($key) {
			$match_item = $country_search['results'][$key];

			var_dump("Price in MXM is" . $match_item["price"] * $currency_exchange_array[$coin]);
			var_dump($match_item);
		}
		
	}

	//Pendiente modify compare_items in order to receive a string in item_reference_id
	//Pendiente $remove_words_relatedto goes to a queue in azure to be used next to improve search 
	/*
	$array_match_titles = $meli -> compare_title_preg_match_title_input($title_base, $titles_array_to_compare, $option_match = array("get_conversion" => true) , $remove_words_relatedto = null)
	$options_work_and_save = array("get_conversion" => true
	$array_match = $meli -> compare_items($item_reference_id, $array_to_compare_example, $options_method_to_compare = array("compare_title" => "preg_match"));

	var_dump($array_match);
	*/

}

echo "<br> Rounded runtime : all_market_search_comparison_model -> " . round($end_time - $start_time, 4) . " seconds";
