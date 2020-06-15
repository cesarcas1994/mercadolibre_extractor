
<?php

/*
* Work automatic access_token - "Construction"
*/

//phpinfo();

//////////////////  "Building Area"   //////////////////////////////////

/**
 * search_model.php
 */

$main_categories= "MLM1648"; // Computacion

$Ropa_Bolsas__Calzado = "MLM1430";

$cajas_de_direccion_categories = "MLM160415"; //30000 articulos

$Suspensión_y_Dirección = "MLM160019";

$Trajes_de_Baño = "MLM115684"; //30000 articulos

$Celulares_y_Smartphones = "MLM1055";

//$result = $meli -> get_children_local($Ropa_Bolsas__Calzado, $options_work_and_save, $category_offset = 8, $max_article_counter = 12000000);

    $tiny_categories = "MLM128748"; //laptops similar a la mia
    $otra_categoria = "MLM190971"; //categoria + 1000 elementos
    $cargada_categoria = "MLM122506"; // categoria +4000 elementos

//$result_total = $meli -> get_articles_total($Trajes_de_Baño, 50, $options_work_and_save);

//$result = $meli -> get_children($main_categories);   
 
//////////////////  "Revision"   //////////////////////////////////

 $item_paused_ej ="MLA723647586"; //Tomar de referencia para revision de json

 $item_cremallera = "MLM658827241";

 $item_example_1 = 'MLM710035327'; //

 $item_example_2 = 'MLM712247366';    ///Estos son piezas de carro 

 $item_example_3 = 'MLM658827241'; //

 $item_example_4 = 'MLM669868088'; //    Estos son celulares

 $item_example_5 = 'MLM672100213'; //

 $user_id_example = "183323305";


 $array_to_compare_example = array($item_example_1, $item_example_3);

/////////////////   "Funciones"    /////////////////////////////////

/// busqueda personalizada ////
//$result = $meli -> get_articles($tiny_categories, true);
//$result = analysis::custom_search("Cafe El Arriero Cubano 1kg Molido", "&limit=3", $country = "MLM");
//$result = analysis::custom_search("tabacos cubanos", "&category=MLM178267", $country = "MLM");
// $result = analysis::custom_search("caja cremallera direccion electroasistida audi a4 2013", "", $country = "MLM");

 //var_dump($result);

 //var_dump($array_to_compare_example);

//$result = analysis::trends_search("MLM1479"); 

//$result = analysis::opinions_item($item_paused_ej);  

//$result = analysis::window_time_user_views($item_cremallera, 10, $unit="day");
//$ending = "2019-02-18"

/*        clase de Post y put revisión         */     

//$user = post::create_test_user("MLM");
//var_dump($user);

/*
$result_Cafe_Serrano  = post::create_item("Cafe Serrano Cubano 1kg Molido", "MLM2354", 640, "MXN", 2, "buy_it_now", "gold_special", "new", "Un café especial para iniciar el día", "YOUTUBE_ID_HERE", "Sellados.", $pictures_url_array = array('http://www.bazar-virtual.cu/images/prod_images/20941_20150218111405.jpg'));

$result_test = post::create_item("Item de test - No Ofertar", "MLM2354", 10, "MXN", 1, "buy_it_now", "gold_special", "new", "Item de test - No Ofertar", null, "Sellados.", $pictures_url_array = array('http://mla-s2-p.mlstatic.com/968521-MLA20805195516_072016-O.jpg'));

var_dump($result_Cafe_Serrano);   

$validador = post::validator_item($body = $result_Cafe_Serrano ); 

var_dump($validador);
*/

//post::match_item_bycategory("MLM2354");

//$result = analysis::window_time_user_views( $user_id_example, 3, $unit="day", $ending = null);
//var_dump($result["results"][1]["visits_detail"]);

/// **  Revisar IBUSHAK    **///

//$result = $meli -> get_articles("MLM1574&nickname=IBUSHAK%20OFICIALES&offset=7400&limit=50&access_token=APP_USR-3049435460117644-110803-9f0c68ab2b9470ebdf61ca6aea26cfd3-268590414");

/////    LLamada directa a DHL     ///
/*
$start_time = microtime(true);

$url = "https://api-eu.dhl.com/track/shipments?trackingNumber=4127563521";

$json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

var_dump($json);

//$obj = json_decode($json, $assoc = true);

$end_time = microtime(true);

echo "<br> direct call time rounded: " . round($end_time - $start_time, 6) . " seconds";

echo "<br>	////////////////////     ////////////////////";

//    metodo funtion Azure 1.3 seg    //

$start_time = microtime(true);

$url = "https://cesartestv5.azurewebsites.net/api/HttpTrigger1?code=WB6EZscX8tv0SvggVQoeDO9zhNM4VfQrqW2tl0SOozCY8RRp5MMBaQ==&DHL=4127563521";

$json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

var_dump($json);

//$obj = json_decode($json, $assoc = true);


$end_time = microtime(true);

echo "<br> indirect call -funtion Azure- time rounded: " . round($end_time - $start_time, 6) . " seconds";
*/
/////    FINAL !!    ///

/**   Search all Lationamerica*/

$change_to_array = $Name_coin = array(
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

//$result = analysis::get_today_currency_exchange("MXN", $change_to_array);
//var_dump($result);

// Store it as a . PDF file in the filesystem

// Get articles total 

//$meli -> get_articles_total($cargada_categoria, 50, $options_work_and_save = array("dont_save" => true));
//$meli -> save_error($save = false);// $save=false It is equivalent to working errors

$result = analysis::get_category_by_ranking_search("rasuradora electrica", $country_id = "MCO");

var_dump("test get_category_by_ranking_search");
var_dump($result);

$item_ids_array = $meli -> get_articles_total_v2($otra_categoria , $total_articles = null, $limit = 50, $country_id = "MLM", $plus = null, $force_calculation_under_1000 = false);

var_dump("item_ids_array");
var_dump($item_ids_array);

//test get_item_time_active

$result = $meli -> get_full_article_array($item_ids_array);

foreach ($result as $key => $value) {
  $time_created_array[$key] = $result[$key]["date_created"];
  $healt_array[$key] = $result[$key]["health"];
}

$item_time_active_array = $meli -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);

//Test sold_quantity

//Pendiente insertar esto en estructura de filtro dependiendo si las ventas son mayor a 5

/* V1

$url_azure_cloud = "https://getsoldquantitymeliv2.azurewebsites.net/api/HttpTrigger1?code=7sRuvOljlYYN9UaILS79qEN2G1nqaCs5pSMAdZS2Bcd8wB1ooFxBZg==";

$url_azure_cloud_v2 = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==";

$url_azure_cloud_v3 = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==";

$search_items = $meli -> get_articles("MLM2354&limit=25");

//var_dump($search_items["results"]);

foreach ($search_items["results"] as $key => $value) {
	var_dump($key);
	var_dump($search_items["results"][$key]["permalink"]);
	$permalinks_array[$key] = $search_items["results"][$key]["permalink"];
}

var_dump($permalinks_array);

$start_time = microtime(true);

$sold_quantity_array =  $meli -> get_sold_quantity_array($permalinks_array, $url_azure_cloud_v2);

$end_time = microtime(true);
echo "<br>Rounded runtime: " . round($end_time - $start_time, 4) . " seconds";

var_dump($sold_quantity_array);

 // Final!!

*/

//Prueba
/*
$result = analysis::custom_search("Llanta P275/65 R18 114t Open Country A/t Toyo Tires", "&limit=20", $country = "MLM", $pass_access_token = false);

foreach ($result["results"] as $key => $value) {
	$item_ids_array[$key] = $result["results"][$key]["id"];
}

$result = $meli -> get_full_article_array($item_ids_array);

foreach ($result as $key => $value) {
	$time_created_array[$key] = $result[$key]["date_created"];
	$healt_array[$key] = $result[$key]["health"];
}

var_dump($time_created_array);
var_dump($healt_array);

$url_azure_cloud = "https://predictmarket.azurewebsites.net/api/itemTimecreated?code=lDYeIva/V1NnW/M8lkBeiGnpZ/OyOp53hSa9WxY5WMLGnIlpIabH1w==";

$item_time_active_array = $meli -> get_item_time_active_array($time_created_array, "days", $healt_array, $url_azure_cloud);

var_dump($item_time_active_array);
*/
//- test funtion conversion

// conversion V2 call,  Pendiente diferenciarlo del otro Conversion de analysis 

/*

$url_azure_cloud_v2 = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==";

$result = analysis::custom_search("1 guía prepagada día siguiente dhl 1kg + recoleccion gratis", "&limit=10", $country = "MLM", $pass_access_token = false);
var_dump($result['results']);

$conversion_array = $meli -> get_conversion_array($result['results'], "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");

var_dump($conversion_array);

/** Tested array to compare words

$search = "cafe cubano serrano";

$titles_array_to_compare = array("cafe cubano serrano 123", "cafesito 2013","cafesito 20 02","cafe para ti y para mi");

$position_match_array = $meli -> compare_title_preg_match_title_input($search, $titles_array_to_compare, $option_match = array("return_position_match" => true) , $remove_words_relatedto = null);

var_dump($position_match_array);
*/

//Testear DHL

        //This format extracts elements in parallel count = get_articles_total -> $ limit.
/*
$item_ids_array = ["8226530395","8226535995","8226527912","5586615635","5586661673","5586665895","586879130","5587151102","5587325240","5587331584","5587370493","5587396091","5587407184","5587433666","5587534385","5587460012","8226240573","5587488266","5587498862","5587497720","5587541820","5589097953","5589202603","5589118544","4919651074","5589123330","5589124111","5589144912","5589202905","5589198790","5589231373","5589231981","4564715562","5589165890","5589171512","5589218202","5589282683","5589284186","5589240672","5589275790","5589211832","5589212182","5589302902","5589226775","5589307566","5589299472","5589371852","5589341693","5589383251","5589315314"];

//$item_ids_array = ["8226530395","8226535995","8226527912","5586615635"];

var_dump($item_ids_array);

//Get call

$start_time = microtime(true);
              
        foreach ($item_ids_array as $key) {
            $url_array[] = "https://prod-10.centralus.logic.azure.com/workflows/e2c516bd8e8645cdae6f424c03077308/triggers/request/paths/invoke/DHL/" . $key . "?api-version=2016-10-01&sp=%2Ftriggers%2Frequest%2Frun&sv=1.0&sig=esTJq01cAhMy-R7ebi8tb6Y1SiQVraE5ukXsIRBw5sk";
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
        	var_dump($key);
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
            var_dump($obj[$key]);
        }

        
        //var_dump($obj[2]) ;

 $end_time = microtime(true);

echo "<br> indirect call -GET- time rounded: " . round($end_time - $start_time, 6) . " seconds";

//Post call

$start_time = microtime(true);
              
        foreach ($item_ids_array as $key) {
            $url_array[$key] = "https://prod-05.centralus.logic.azure.com:443/workflows/df3038cb735b4807bcc7939b56bd508c/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=pXj4aXu9xnBCb5dZrTpp6Rz45r_mYUiweiLoT8P8ja8";
        }

        //Pendiente hacer varias llamadas post al mismo tiempo asincrono.

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
        	var_dump($key);
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
            var_dump($obj[$key]);
        }

        
    //var_dump($obj[2]);

$end_time = microtime(true);

echo "<br> indirect call -POST - time rounded: " . round($end_time - $start_time, 6) . " seconds";

// end !! Testear DHL
*/
/*
$number_of_days = 15;

$polynomial_order = 3;

$result = analysis::custom_search("1 guía prepagada día siguiente dhl 1kg + recoleccion gratis", "&limit=10", $country = "MLM", $pass_access_token = false);
//var_dump($result['results']);

foreach ($result['results'] as $key => $value) {
	
	$item_ids_array[$key] = $result["results"][$key]["id"];
	var_dump($result["results"][$key]["title"]);
}

//var_dump($item_ids_array);

$visits_item_ndays =  analysis::visits_item_ndays($item_ids_array, $number_of_days);

// predictmarket

var_dump($visits_item_ndays[$item_ids_array[0]]);

$Azure_Polynomial_Regretion = "https://predictmarket.azurewebsites.net/api/HttpTrigger1?code=AQYsDAc0y1o5bAWbfrIarVM2Kx1PphycUoqm9R747BoajaVk/aNJEw==";

$body = array(
	"data" => $visits_item_ndays[$item_ids_array[0]],
	"predict_value" => $number_of_days,
	"polynomial_order" => $polynomial_order
);

$body = json_encode($body);

$CURL_OPTS = array(
	//revisar si es necesario esta linea
    //CURLOPT_USERAGENT => "MELI-PHP-SDK-2.0.0", 
    // revisar si es necesario que este en true 
    //CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_CONNECTTIMEOUT => 10, 
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_TIMEOUT => 60
);

$opts = array(
    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
    CURLOPT_POST => true, 
    CURLOPT_POSTFIELDS => $body
);

$uri = $Azure_Polynomial_Regretion;

$ch = curl_init($uri);
curl_setopt_array($ch, $CURL_OPTS);
curl_setopt_array($ch, $opts);

$return["body"] = json_decode(curl_exec($ch), true);
$return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);	

curl_close($ch);

var_dump($return);


/*
public static function post_item($path = '/items', $body = null, $params = array()) {
    $body = json_encode($body);
    $opts = array(
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_POST => true, 
        CURLOPT_POSTFIELDS => $body
    );
    
    $exec = post::post_execute($path, $opts, $params);

    return $exec;
}

public static function post_execute($path, $opts = array(), $params = array(), $assoc = false) {
        $uri = post::post_make_path($path, $params);

        $ch = curl_init($uri);
        curl_setopt_array($ch, self::$CURL_OPTS);

        if(!empty($opts))
            curl_setopt_array($ch, $opts);

        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        return $return;
    }
*/    

//var_dump($visits_item_90days);

/** all_market_search_comparison_model */  

/*

$start_time = microtime(true);

$result = $meli -> custom_generalmatch_search("café serrano", $plus = null, $return_object = false);
var_dump($result);  

$end_time = microtime(true);

echo "<br> indirect call - all_market_search_comparison_model- time rounded: " . round($end_time - $start_time, 6) . " seconds";
