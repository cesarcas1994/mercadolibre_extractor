<?php

/**
 * analysis_model
 */
class Analysis extends Meli
{
	protected static $GET_USER_ID_URL = "https://api.mercadolibre.com/users/";
	protected static $GET_SEARCH_URL = "https://api.mercadolibre.com/sites/MLM/search?q=";
	protected static $GET_TENDS_URL = "https://api.mercadolibre.com/trends/MLM/";
	protected static $GET_REVIEWS_URL = "https://api.mercadolibre.com/reviews/item/";
	protected static $GET_VISITS_TIME_WINDOW_URL = "/visits/time_window"; 
	protected static $GET_VISITS_ARRAY = "https://api.mercadolibre.com/items/visits?ids=";
    protected static $GET_SELLER_INFORMATION = "https://api.mercadolibre.com/users/";

    /*****************************************************************
                              ACCESS_TOKEN
    *****************************************************************/

    /*
      $options_access_token[access_token]
        get_count => true // return the number of calls to obtein access_token in api.    
    */                          

    public static function access_token($options_access_token = array()){

        $access_token = "APP_USR-3049435460117644-010302-87d91659c9054a9615e541fac9dc9f41-458700815";

        static  $count_called_token = 0;

        static $firs_call_time = 0;

        //get_count $options_access_token = array('get_count' => true)
        if (array_key_exists('get_count', $options_access_token) &&  $options_access_token['get_count'] == true) {
            return $count_called_token;            
        }

        if ($count_called_token == 0){          
            $firs_call_time = analysis::today($exact_time = true); 
        }

        $count_called_token = $count_called_token + 1;

        //var_dump("This is the token call number: ");
        //var_dump($count_called_token);

        $call_hit_time = analysis::today($exact_time = true);

        //var_dump("Time difference since start is: ");
        //var_dump(($call_hit_time['minutes'] - $firs_call_time['minutes']) . " minutes and" . ($call_hit_time['seconds'] - $firs_call_time['seconds']) . " seconds");


        if (!($call_hit_time['hours'] - $firs_call_time['hours'] > 0 && $call_hit_time['minutes'] > $firs_call_time['minutes']) && $count_called_token > 7000) {

           echo "Beware the number of token calls has exceeded 7000 calls in:" . ($call_hit_time['hours'] - $firs_call_time['hours']) . " hours and " . ($call_hit_time['minutes'] - $firs_call_time['minutes']) . " minutes";

           echo "<br>Caution the run will stop if it exceeds 8k calls per hour";
        }

        if (!($call_hit_time['hours'] - $firs_call_time['hours'] > 0 && $call_hit_time['minutes'] > $firs_call_time['minutes']) &&  $count_called_token > 7950) {
            var_dump("Suspended the script for having exceeded 7950 calls to the token in the first hour of running");

            exit(1);
            
        }

        //Test is done with respect to the first hour of running since it is shown that the run tends to stabilize around a number of calls over time

        return $access_token;
    }                          

	public static function today($exact_time = null){
        //pendiente poner variable de entrada como string o array
		$fecha = getdate();
        //return a string
        if ($exact_time == null) {
            return $fecha['year'] . "-" . $fecha['mon'] . "-" . $fecha['mday'] . "T00:00:00.000-00:00"; //r
        }
        //return a array
        else{
            return $fecha; //It return complete array of exact_time including hours, minutes and seconds
        }
		
	} 

    public static function format_yyyy_mm_dd($time_array)
    {
        foreach ($time_array as $key => $value) {
            $portions = explode("T", $time_array[$key]);
            $time_array_format_yyyy_mm_dd[$key] = $portions[0];
        }

        return $time_array_format_yyyy_mm_dd;
    }

    public static function date_mm_dd($date_yyyy_mm_dd){

        //not array function

        $portions = explode("-", $date_yyyy_mm_dd);

        //var_dump($portions);
        $pieces[0] = $portions[1]; //mm
        $pieces[1] = $portions[2];  //dd
        $date_mm_dd = implode("-", $pieces);

        //var_dump($date_mm_dd);
        return $date_mm_dd;
        
    }

    public static function traduce_to_pt($input_text){

        //Pendiente pasar todo el rollo curl post a clase post y simplificar la clase analysis

        $body = array(
            "text" => $input_text
        );

        $Azure_translateText = "https://mlconexionmeli.azurewebsites.net/api/translateText?code=Yv7pN8h8LjmuJZnxFf7lUgZsz0teX5dHA9aiajlaarSGrAyoxXLBBw==";

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

        $uri = $Azure_translateText;

        $ch = curl_init($uri);
        curl_setopt_array($ch, $CURL_OPTS);
        curl_setopt_array($ch, $opts);

        $body_response = json_decode(curl_exec($ch), true);
        $httpCode_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);    

        curl_close($ch);

        $pt_text = $body_response[0]["translations"][0]["text"];

        return $pt_text;
    }

    public static function traduce_to_pt_v2($input_text_array){

      $Azure_translateText = "https://mlconexionmeli.azurewebsites.net/api/translateText?code=Yv7pN8h8LjmuJZnxFf7lUgZsz0teX5dHA9aiajlaarSGrAyoxXLBBw==";

      foreach ($input_text_array as $key => $value) {
        
        $input_text_array[$key] = analysis::separator_search($input_text_array[$key]);
        $url_array[] = $Azure_translateText . "&text=" . $input_text_array[$key];
      }

      //var_dump($url_array);
      
      $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
      
      foreach ($json_array as $key => $value) {
          $obj[$key] = json_decode($json_array[$key], $assoc = true);
      }
      
      foreach ($obj as $key => $value) {
          $pt_text_array[$key] = $obj[$key][0]["translations"][0]["text"];
      }

      return $pt_text_array;
      
    }

    public static function time_trigger_data($time){
        
    }

    public static $AUTH_URL = array(
        "MLA" => "https://auth.mercadolibre.com.ar", // Argentina 
        "MLB" => "https://auth.mercadolivre.com.br", // Brasil
        "MCO" => "https://auth.mercadolibre.com.co", // Colombia
        "MCR" => "https://auth.mercadolibre.com.cr", // Costa Rica
        "MEC" => "https://auth.mercadolibre.com.ec", // Ecuador
        "MLC" => "https://auth.mercadolibre.cl", // Chile
        "MLM" => "https://auth.mercadolibre.com.mx", // Mexico
        "MLU" => "https://auth.mercadolibre.com.uy", // Uruguay
        "MLV" => "https://auth.mercadolibre.com.ve", // Venezuela
        "MPA" => "https://auth.mercadolibre.com.pa", // Panama
        "MPE" => "https://auth.mercadolibre.com.pe", // Peru
        "MPT" => "https://auth.mercadolibre.com.pt", // Prtugal
        "MRD" => "https://auth.mercadolibre.com.do"  // Dominicana
    );

    public static $Name_coin = array(
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
        //"MPT" => "EUR", // Prtugal out of market
        "MRD" => "DOP"  // Dominicana
    );

    public static function  get_today_currency_exchange($base_exchange, $change_to_array){

        $api_key_cesar = "2664|J8sVfhUvo*bb3dR4hSyk^LpjuK0AE4Kf";
        $url_api = "https://api.cambio.today/v1/quotes/";
        $i = 0;
        foreach ($change_to_array as $key) {
            $url_array[] = $url_api . $key . "/" . $base_exchange . "/json?quantity=1&key=" . $api_key_cesar;
        }

        //var_dump("test url array");
        //var_dump($url_array);

         $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        //var_dump("result api money");
        //var_dump($obj);

        foreach ($change_to_array as $key => $value) {

            $today_currency_exchange[$key] = $obj[$i]["result"]["value"];
            $i = $i + 1; 
        }

        return $today_currency_exchange;

    } 

    public static function translate_azure($text){

        //Pendiente necesita revision esta funcion

        $url_translate_api_azure = "https://translatemeli.cognitiveservices.azure.com/sts/v1.0/issuetoken";

        $key = "a79806818c7143aca96bc7bbd6489e53";

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
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Ocp-Apim-Subscription-Key: $key'),
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
    }



	/*********************             Process Data              **********************************/

	public static function get_full_article_inactive_array($item_ids_array)
    {

        //Remember to activate the search for inactive items change in meli:
          // work_and_save($parent_category, $item_ids_array, $dir_save, $get_inactive = false) 
        //for $get_inactive = true;
              
        $obj = analysis::get_full_array_toprocess($item_ids_array);

          $obj_inactive = array(); 
             
          $status_var=0;
          foreach ($obj as $key => $value) {
          		//var_dump($key); // active
              if ($obj[$key]['status'] != 'active') 
              { var_dump("Encontrado inactivos");   
              	var_dump($obj[$key]); 
                $obj_inactive[$status_var] = $obj[$key];
                 $status_var = $status_var + 1;
              }
          }  
        var_dump($obj_inactive);      
        return $obj_inactive;
    }

    public static function get_full_article_conversion_array($item_ids_array, $analysis_options = array()){

    	$obj = analysis::get_full_array_toprocess($item_ids_array);
    	
    	$visits_array = analysis::visits_time_window_array($item_ids_array); 

    	$offset = analysis::save_count_onprocess($count = count($obj));

    	static $obj_conversion;

    	var_dump("conversion offset");
    	var_dump($offset);

        var_dump("item_ids_array_conversion");
        var_dump($item_ids_array);
    	
		foreach ($obj as $key => $value) {	

			//filter ex: objects published only in 2019 Yes !!! Let's do it

			//if (strpos($obj[$key]["date_created"], '2019') !== false) {

            //Pendiente este filtro sea opcional

				// Pendiente arreglar que se guarde en orden ej 0-46 sin huercos 

				var_dump("times bought"); //Posible idea value putting uncertainty and spread uncertainty
				var_dump($obj[$key]['sold_quantity']);
				var_dump("views");
				var_dump($visits_array[$key]);

				if ($visits_array[$key] == 0) {	$conversion = 0;}
				else {$conversion = ($obj[$key]['sold_quantity']/$visits_array[$key]);}
				

				$obj_conversion[$key + $offset + 1] = array(
					"item_id" => $obj[$key]['id'],
					"title" => $obj[$key]['title'],
					"conversion" => $conversion, 
			        "views" => $visits_array[$key],
			        "solds" => $obj[$key]['sold_quantity'],
                    "price" => $obj[$key]['price']
    			);
			
			//}
							    
        }

        $obj_conversion = analysis::process_data($obj_conversion, $analysis_options);
        
        //Take $ obj_conversion to a function that eats data and organizes it based on its $analysis_options.  

       	return $obj_conversion;    

    }

    public static function get_full_array_toprocess($item_ids_array){

    	//Performance: it takes to work an array of 50 elements of average [0.4-0.5] seconds.

    	foreach ($item_ids_array as $key) {
            $url_array[] = parent::$GET_FULL_ARTICLE_URL . $key;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        

        foreach ($json_array as $key => $value) {

            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;
    }

    public static function process_data($obj_conversion, $analysis_options = array()){

    	//Get a list of columns for the array_multisort functions
        // Pendiente cambiar nombre de esta funcion porque es demasiado general para el tema tan especifico que toca

    	foreach ($obj_conversion as $key => $row) {

    		$item_id[$key] = $row['item_id'];
		    $conversion[$key] = analysis::significant_numbers($row['conversion']);	    
		    $views[$key] = $row['views'];
		    $solds[$key] = $row['solds'];
		}

   		 if (array_key_exists('more_views', $analysis_options) &&  $analysis_options['more_views'] == true) {

   		 	array_multisort($views, SORT_DESC, $conversion, SORT_DESC, $obj_conversion);

        }
  		  elseif (array_key_exists('more_solds', $analysis_options) &&  $analysis_options['more_solds'] == true){

  		  	array_multisort($solds, SORT_DESC, $conversion, SORT_DESC, $obj_conversion);
            
        }
  		  elseif (array_key_exists('more_conversion', $analysis_options) &&  $analysis_options['more_conversion'] == true){

  		  	array_multisort($conversion, SORT_DESC, $solds, SORT_DESC, $views, SORT_ASC, $obj_conversion);
            
        }

        //var_dump($obj_conversion);

        return $obj_conversion;
    }

    public static function save_count_onprocess($count){
    	static $expansion_count = 0;

    	if ($expansion_count == 0) { $expansion_count = $expansion_count + $count; return $expansion_count - $count;}
        else{$expansion_count = $expansion_count + $count; return $expansion_count - $count;}
    }

    public static function sort_localdata ($dir, $analysis_options = array()){

    //Remember Add function change "/" by the stick that is the other way around and viceversa

    $local = file_get_contents("C:/" . $dir);	

    $obj_to_organize = json_decode($local, $assoc=true);

    $obj_conversion = analysis::process_data($obj_to_organize, $analysis_options);

    return $obj_conversion;

    }

    public static function significant_numbers($value){

        if ($value > 1) {$round_value = round($value, 1);}
        elseif ( 0.1 < $value &&  $value <= 1) { $round_value = round($value, 1);}
        elseif ( 0.01 < $value && $value <= 0.1) { $round_value = round($value, 2);}
        elseif ( 0.001 < $value && $value <= 0.01) { $round_value = round($value, 3);}
        else{$round_value = round( $value, 4);}

        return $round_value;

    }

    /*********************         Finished Process Data          **********************************/

    public static function custom_search($search, $plus, $country = "MLM", $pass_access_token = true)
    {


      $search = analysis::separator_search($search);	
      if ( $pass_access_token == false) {   $access_token = null;}
      else{$access_token = analysis::access_token();}  
        
      $url = "https://api.mercadolibre.com/sites/" . $country . "/search?q=" . $search . $plus . "&access_token=" . $access_token;

      $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

      $obj = json_decode($json, true);

      return $obj;

    }

    public static function custom_search_array($search, $plus_array, $country = "MLM", $pass_access_token = true)
    {

        $search = analysis::separator_search($search);
        if ( $pass_access_token == false) {   $access_token = null;}

        foreach ($plus_array as $key) {
            $url_array[] = "https://api.mercadolibre.com/sites/" . $country . "/search?q=" . $search . $key . "&access_token=" . $access_token;
        }

        //var_dump($url_array);

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;
    }

    public static function custom_search_array_v2($search_title_array, $plus, $country){

        foreach ($search_title_array as $key => $value) {
            $search_title_array[$key] = analysis::separator_search($search_title_array[$key]);
            $url_array[] = "https://api.mercadolibre.com/sites/" . $country . "/search?q=" . $search_title_array[$key] . $plus;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;
    }

    public static function get_seller($seller_id_array){

        foreach ($seller_id_array as $key) {
            $url_array[] = self::$GET_SELLER_INFORMATION . $key;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);                 
        }

        return $obj;

    }

    public static function trends_search($category){

        $url = self::$GET_TENDS_URL . $category;	

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, true);

        return $obj;

    }


    public static function trends_search_array($category_ids_array){

        foreach ($item_ids_array as $key) {
                $url_array[] = self::$GET_TENDS_URL . $key;
        }    
        
        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
            
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;

    }

    public static function opinions_item($item){
    	
        //This parameter does not give much information

        $url = self::$GET_REVIEWS_URL . $item;

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, true);

        return $obj;

    }

    public static function opinions_item_array($item_ids_array){

        foreach ($item_ids_array as $key) {
                $url_array[] = self::$GET_REVIEWS_URL . $key;
        } 

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
            
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;

    }

    public static function ask_questions($item_id, $text)
    {


    }

    public static function visits_time_window_array($item_ids_array, $start = '2019-01-01T00:00:00.000-00:00', $ending = null){
    
    	//Performance: it takes to work an array of 50 elements of average 0.4 seconds

    	if($ending == null){
    		$ending = analysis::today($exact_time = null);
    	}

    	foreach ($item_ids_array as $key) {
            $url_array[] = self::$GET_VISITS_ARRAY . $key . "&date_from=" . $start . "&date_to=" . $ending;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        foreach ($json_array as $key => $value) {

            $obj[$key] = json_decode($json_array[$key], true);
        }

        foreach ($obj as $key => $value) {
        	$visits_array[$key] = $obj[$key][0]['total_visits'];
        }

        return $visits_array;

    }

    // Receive only one item

    public static function window_time_views($item, $last, $unit="day", $ending = null){
    
    	if($ending != null){$ending = "&ending=" . $ending;}

    	$url= parent::$GET_FULL_ARTICLE_URL . $item . self::$GET_VISITS_TIME_WINDOW_URL . "?last=" . $last . "&unit=" . $unit . $ending;

    	$json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

    	$obj = json_decode($json, true);

        return $obj;

    }

    public static function window_time_user_views($user_id, $last, $unit="day", $ending = null){
    
        if($ending != null){$ending = "&ending=" . $ending;}

        $url= self::$GET_USER_ID_URL . $user_id . "/items_visits/time_window" . "?last=" . $last . "&unit=" . $unit . $ending;	

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, true);

        return $obj;

    }

    public static function window_time_items_views_array($item_ids_array, $last, $unit="day", $ending = null)
    {
        if($ending != null){$ending = "&ending=" . $ending;}

        foreach ($item_ids_array as $key) {
          $url_array[] = "https://api.mercadolibre.com/items/" . $key . "/visits/time_window" . "?last=" .
          $last . "&unit=" . $unit . $ending;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));    
        foreach ($json_array as $key => $value) {
           $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj; 

    }

    public static function total_item_visions($item_id){

        $url = "https://api.mercadolibre.com/visits/items?ids=" . $item_id;

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, true);

        return $obj[$item_id];

    }

    public static function total_item_visions_array($item_id_array){

        foreach ($item_id_array as $key) {
          $url_array[] = "https://api.mercadolibre.com/visits/items?ids=" . $key;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));    
        foreach ($json_array as $key => $value) {
           $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;  
    }
    
    public static function separator_search($search){
    	// Change blanks in a sentence for "% 20"
        
    	$portions = explode(" ", $search);
    	$union = implode("%20", $portions);

    	return $union; 

    }

    public static function at_least_one_word_comparation($first_title, $second_title){

        $portions1 = explode(" ", $first_title);
        $portions2 = explode(" ", $second_title);

        if(array_intersect($portions1, $portions2) != null){
            $response = true; 
        }
        else{
            $response = false;
        }
        
        return $response;
    }

    public static function available_filters($available_filters, $filter_option = array()){

        //Take from $available_filters whitch information you want, It was created with the objective of ensuring that the desired filter is obtained since there are times that it is not registered in the same order

        if (array_key_exists('category', $filter_option) &&  $filter_option['category'] == true){

            foreach ($available_filters as $x => $value){

                if ($available_filters[$x]["id"] === "category" ){
                    $category_available_filters = $available_filters[$x];
                    break;                
            }

            if ($category_available_filters === null){$category_available_filters = "It has not registered in the filters the characteristic category";}
            //This line marks a warning because the search is null
            }

            return $category_available_filters;

        }
        else {
            return "This function has a declaration error, check how the filter to be searched is entered.";
        }
    }

    public static function get_nickname($seller_id_or_nickname = array()){

      if (array_key_exists('seller_id', $seller_id_or_nickname)){
        $seller_id_array[0] = $seller_id_or_nickname['seller_id'];
        $seller_info = analysis::get_seller($seller_id_array);
        $nickname = $seller_info[0]['nickname'];
       }
       elseif (array_key_exists('nickname', $seller_id_or_nickname)){
        $nickname = $seller_id_or_nickname['nickname'];
       }
       else { echo "Introducir correctamente la variable de entrada $seller_id_or_nickname"; 
       }

       return $nickname;
    }

    public static function get_category_features($category_id){

      $url = self::$GET_CHILDREN_URL . $category_id;

      $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));
       
      $obj = json_decode($json, $assoc = true);

      return $obj;
    }

    public static function get_father_position_list($father_category_id){

        if ($country_base = "MLM") {
         $fathers_of_children_categories = $this -> get_all_fathers_of_children_categories_mexico_local();
        }
        else{
         return "just $country_base for MLM mexico";
        }

        foreach ($fathers_of_children_categories as $key => $value) {

            if ($fathers_of_children_categories[$key] == $father_category_id) {
                
               $father_category["min_list"] = $key;
               $father_category["max_list"] = $key; 

               break;
            }
        }

        return $father_category;
    }

    public static function get_category_by_ranking_search($title, $country_id){

       $obj = analysis::custom_search($title, "&limit=1", $country_id, $pass_access_token = false);    

       $predicted_category = $obj["results"][0]["category_id"];

       //testing

       var_dump("item_to_search_category");
       var_dump($obj["results"][0]["title"]);

        $category_features = analysis::get_category_features($predicted_category);

        var_dump($category_features["path_from_root"]);

       return $predicted_category;
    }

    public static function get_category_predictor($title, $country_id){

        //https://api.mercadolibre.com/sites/MLB/category_predictor/predict?title=Ipod

        $title_search = analysis::separator_search($title);

        $url = "https://api.mercadolibre.com/sites/" . $country_id . "/category_predictor/predict?title=" . $title_search;

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, true);

        // main features

        $predicted_category["id"] = $obj["id"];
        $predicted_category["name"] = $obj["name"];

        //secondary features
       
        $prediction_probability = $obj["prediction_probability"];

        $path_from_root = "";
        foreach ($obj["path_from_root"] as $key => $value) {
            if ($key == count($obj["path_from_root"]) - 2) {continue;} //delete the penultimate predicted category to improve the subsequent search procedure

            $portion = $obj["path_from_root"][$key]["name"];
            if ($path_from_root == "") {
             $path_from_root = $portion;
            }
            else{
             $path_from_root = $path_from_root . " " .  $portion;
            }   
        }

        $predicted_category["full_name"] = $path_from_root;   

        var_dump("category predictor : " . $country_id);
        var_dump($path_from_root);
        
        var_dump($predicted_category["name"]);
        var_dump($prediction_probability);
        
        /*if($prediction_probability < 0.2){

            var_dump("country : " . $country_id . " title : " . $title . " does not fit in category predictor");
            return null;
        }*/

        return $predicted_category;
    }

    public static function get_category_predictor_v2($title_array, $country_id){

        foreach ($title_array as $key) {

          $title_search = analysis::separator_search($key);  
          $url_array[] = "https://api.mercadolibre.com/sites/" . $country_id . "/category_predictor/predict?title=" . $title_search;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));  

        foreach ($json_array as $key => $value) {
           $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        foreach ($obj as $key => $value) {
            $predicted_category[$key]["id"] = $obj[$key]["id"];
            $predicted_category[$key]["name"] = $obj[$key]["name"];
        }

        return $predicted_category; 
    }



    ///////// data projection methods //////////

    public static function visits_item_ndays($item_ids_array, $number_of_days){

        //return matrix (x -> $number_of_days, y -> count($item_ids_array))

       $search_visits =  analysis::window_time_items_views_array($item_ids_array, $number_of_days, $unit="day", $ending = null);

        foreach ($search_visits as $key => $value) {
            $item_id = $search_visits[$key]["item_id"];
            
            foreach ($search_visits[$key]["results"] as $day => $visits_array) {

               $visits_item_ndays[$item_id][$day] = [$day, $search_visits[$key]["results"][$day]["total"]];

            }         
        }

        return $visits_item_ndays;

    }

    public static function visits_item_ndays_v2($item_ids_array){

        //Pendiente valorar por que empezar a marcar desde el 1 de enero del año presente
        // today 
        $today_time_array_format = analysis::today($exact_time = true);
        $today_time = $today_time_array_format['year'] . "-" . $today_time_array_format['mon'] . "-" . $today_time_array_format['mday'];

        //var_dump("today_time");
        //var_dump($today_time);

        // first day of the year

        $first_day_of_the_year = $today_time_array_format['year'] . "-" . "1" . "-" . "1";

        //var_dump("first_day_of_the_year");
        //var_dump($first_day_of_the_year);

        // how many days of difference

        $diff = abs(strtotime($today_time) - strtotime($first_day_of_the_year));
        $years = $diff / (365*60*60*24);
        $months = $diff / (30*60*60*24);
        $days = $diff / (60*60*24);

        if (($today_time_array_format['year'] % 4 == 0) && ($today_time_array_format['mon'] > 2)) {
            $days_of_this_year = intval($days + 1); //leap-year
            $leap_year = true;
        }
        else{
            $days_of_this_year = intval($days + 1); 
        }

        //var_dump("days_of_this_year");
        //var_dump($days_of_this_year);         
        
        //organized array 0 correspond to 1 january of the year (move offset) 
        $search_visits =  analysis::window_time_items_views_array($item_ids_array, $number_of_days = 365, $unit="day", $ending = null);
        
        foreach ($search_visits as $key => $value) {
            $item_id = $search_visits[$key]["item_id"];

            foreach ($search_visits[$key]["results"] as $key2 => $value2) {

              // for leap_year call to api visits will return 365 values +1 for leap_year -1 dont count today visits
              // for not leap_year call to api visits will return 364 values -1 dont count today visits

              //filter for leap_year and cycle (finish of the year)
              
              if ($leap_year == true) {

                  $offset_day = $key2 + (366 - $days_of_this_year);

                  if ($offset_day >= 365) {
                      $offset_day = $offset_day - 365;
                  }
              }
              else{

                  $offset_day = $key2 + (365 - $days_of_this_year); //check this,could break

                  if ($offset_day >= 364) {
                      $offset_day = $offset_day - 364;
                  }
              }
              

              //var_dump("offset_day");
              //var_dump($offset_day);             

               $date_format_yyyy_mm_dd = analysis::format_yyyy_mm_dd(array(0 => $search_visits[$key]["results"][$offset_day]["date"]));              
                
               $search_visits_v2[$item_id][$date_format_yyyy_mm_dd[0]]["date"] = $date_format_yyyy_mm_dd[0];
               $search_visits_v2[$item_id][$date_format_yyyy_mm_dd[0]]["visits"] = $search_visits[$key]["results"][$offset_day]["total"];
            }
        }
        

        //var_dump($search_visits_v2);

        return $search_visits_v2;
        
    }

    /*********************     Call apis to process information     **********************************/


    public static  function picture_comparation($base_link_pictore, $pictores_links_array_to_compare, $sort_distance = false){

        foreach ($pictores_links_array_to_compare as $key) {
          $url_array[] = "https://itemmatchmeli.azurewebsites.net/api/HttpTrigger2?code=kiTivN3xzKrokrROhbYA25G9fy7oSVoegvvul2uP0wfF04lZ/2gmQw==" . "&image1=" . $base_link_pictore . "&image2=" . $key;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));    

        foreach ($json_array as $key => $value) {

           $obj[$key] = json_decode($json_array[$key], $assoc = true);
           $image_distance[$key] = $obj[$key]["output"]["distance"];
           $image_without_sort[$key] = $image_distance[$key];
        }

        if ($sort_distance == true) {
            sort( $image_distance);
        }

        //organize for sorting
        var_dump("image_distance");
        var_dump($image_distance);
        var_dump("image_without_sort");
        var_dump($image_without_sort);

        foreach ($image_distance as $value1 => $sort) {

            foreach ($image_without_sort as $value2 => $unsort) {
                if ($sort == $unsort) {
                 $image_distance[$value1][0] = $pictores_links_array_to_compare[$value2];
                 var_dump($pictores_links_array_to_compare[$value2]);
                 var_dump($image_distance);
                 continue 2;
                }
            }


        }

        return $image_distance; 
    }


    
}