<?php

/**
 * ibushak_model
 */

//needs access_token_declarations

$ibushak_item_example = "MLM589153702"; //Memoria Sd Uhs-i 32gb Clase 10 Premier Mayoreo Adata
$ibushak_seller_id = "154901871";  
$ibushak_nickname = "IBUSHAK OFICIALES";
$cesar_nickname = "CESARCAS1994"; 

$test_seller_id = "183323305"; // humidores cafe serrano seller_id

$DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/"; 

$dir_to_save = $DEFAULT_SAVE_DIR . $ibushak_nickname . '/';

$dir_to_save_conversion = $DEFAULT_SAVE_DIR . $ibushak_nickname . '/' . 'conversion' . '/';

//$meli -> get_seller_articles_total($seller_id_or_nickname = array('nickname' => $ibushak_nickname), $plus = null, $country = 'MLM', $options_work_and_save = array("dir_to_save" => $dir_to_save));

/*
$seller_articles_total = $meli -> get_seller_articles_total_v2($test_seller_id, $plus = null, $country = 'MLM');

var_dump(count($seller_articles_total));
var_dump($seller_articles_total);
*/

//$table_array = $meli -> fulfillment_calculator_seller_id($test_seller_id);

//$table_array = $meli -> fulfillment_calculator_item_id($item_id = "MLM562479258"); //cafe serrano
//$table_array = $meli -> fulfillment_calculator_item_id($item_id = "MLM612753157"); //Rasuradora Electrica Remington Cortadora Cabello Vello 9pzas, category = MLM4605
//$table_array = $meli -> fulfillment_calculator_item_id($item_id = "MLM753709663");
//Alimento Royal Canin Veterinary Diet Canine Hepatic Perro Adulto 12kg
$table_array = $meli -> fulfillment_calculator_item_id($item_id = "MLM759016953");
// Alimento Royal Canin Breed Health Nutrition Yorkshire Terrier Perro Adulto Raza Pequeña 4.5kg


var_dump($table_array);

// Download the ibushak's mexican data we want

/* Here you can have two variants to save the information.*/

    // 1 - save a single json with all the items by category.
    // 2 - save for each category many json each one with an item.

    // selecting option number 1 (This option need to be fixed)
    /*
    $options_work_and_save = array("get_conversion" => true, "dir_to_save" => $dir_to_save_conversion, "more_solds" => true, "dont_save" => true)
    */
    // selecting option number 2
    /*
    $options_work_and_save = array("dir_to_save" => $dir_to_save)
*/

