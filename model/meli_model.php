<?php
/**
 * meli_model
 */

/**
   * @version 1.0.2
   */
    const VERSION  = "1.0.2";


class Meli
{
    public function __construct()
    {
      
        require_once("model/melicurl_model.php");
        require_once("model/analysis_model.php");
        require_once("model/local_model.php");
        require_once("model/post_model.php");

    }
    
    protected static $GET_CHILDREN_URL = "https://api.mercadolibre.com/categories/";
    protected static $GET_SELLER_ARTICLES   = "/search?nickname=";
    //Pendiente quitar dependendencia de país y poner el pais como variable
    protected static $GET_ARTICLES_URL = "https://api.mercadolibre.com/sites/MLM/search?category=";
    protected static $GET_FULL_ARTICLE_URL = "https://api.mercadolibre.com/items/";
    protected static $SAVE_ARTICLES_DIR_Computacion = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Computacion/";
    protected static $SAVE_ARTICLES_DIR_Inactive_Computacion = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Inactive/Computacion/";
    protected static $SAVE_ARTICLES_DIR_Cajas_de_Dirección = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Accesorios para Vehículos/Refacciones Autos y Camionetas/Suspensión y Dirección/Cajas de Dirección/";
     protected static $SAVE_ARTICLES_DIR_Suspensión_y_Dirección = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Accesorios para Vehículos/Refacciones Autos y Camionetas/Suspensión y Dirección/";
    protected static $SAVE_ARTICLES_DIR_Ropa_Bolsas__Calzado = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Ropa_Bolsas__Calzado/";
    protected static $SAVE_ARTICLES_PRUEBA_EXPANSION = "wamp64/www/cursoPHP/mercadolibre_comparador/data/prueba_expansion/Suspensión y Dirección/";
    protected static $SAVE_ARTICLES = "wamp64/www/cursoPHP/mercadolibre_comparador/data/Modeloprediccion/";
    protected static $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";

    //Pendiente Demasiadas variables ->modificar poner en variable de funcion
    //Pendiente Arreglar todo el modelo de salvado no es lo suficientemente claro
    //Pendiente pensar si poner en cada función que tipo de valor devuelve esto ayuda mucho a un tercero a entender el codigo

    

    public function get_children($father_category)
    {
        //look at the api of mercadolibre for the characteristics of the incoming parent category.

        $url = self::$GET_CHILDREN_URL . $father_category;

        //This method looks for the json of the url and turns it into an object, $ assoc = true passes it from object to associative array.
       
        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));
       
        $obj = json_decode($json, $assoc = true);

        $count_children = count($obj["children_categories"]);
        
        for ($i=0; $i < $count_children; $i++) {
            $children_categories[$i] = $obj["children_categories"][$i]["id"];
        }
        
        return $children_categories;
    } 

    public function get_children_array($fathers_category_array)  {

      foreach ($fathers_category_array as $key) {
          $url_array[] = self::$GET_CHILDREN_URL . $key;
      }

      $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
      
      foreach ($json_array as $key => $value) {
          $obj[$key] = json_decode($json_array[$key], $assoc = true);

          foreach ($obj[$key]["children_categories"] as $i => $value) {
            $children_categories[$key][$i] = $obj[$key]["children_categories"][$i]["id"];
          }
      }

      return $children_categories;

    }

    public function get_father_object_local($children_category){

      $local_file = 'categoriesMLM.json';
  
      $local_json_encode = file_get_contents(__DIR__ . "/../categoriesMLM.json");

      $obj = json_decode($local_json_encode, $assoc = true);

      $number_category_father = count($obj[$children_category]["path_from_root"]) - 2; // it star at 0, and father position is 1 less, sum 2.

      if ($number_category_father < 0) {
        return "this is a major_father_categorie";
      }

      $father_category = $obj[$children_category]["path_from_root"][$number_category_father]["id"];

      $father_category_object = $obj[$father_category];

      return $father_category_object;
    }

    public function get_category_object($category_array){

      foreach ($category_array as $key) {
          $url_array[] = self::$GET_CHILDREN_URL . $key;
      }

      //var_dump($url_array);

      $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
      
      foreach ($json_array as $key => $value) {
          $obj[$key] = json_decode($json_array[$key], $assoc = true);
      }

      return $obj;
    }

    public function get_articles($category)
    {
        // only for MLM (MEXICO)
        //This first article json format is short (little info).
        //This call adjusts limit, offset and access_token to call all items in the category up to 10,000 items max.

        $url = self::$GET_ARTICLES_URL . $category;

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, $assoc = true);

        return $obj;
    }

    public function get_country_articles($category, $country_id)
    {

        $url = "https://api.mercadolibre.com/sites/" . $country_id . "/search?category=" . $category;

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, $assoc = true);

        return $obj;
    }

    public function get_articles_array($category_array){

      foreach ($category_array as $key) {
            $url_array[] = self::$GET_ARTICLES_URL . $key;
        }

        //var_dump($url_array);

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;
    }

    public function get_real_articles_array_country($category_array, $country_id, $return_object = false){
      //this function return articles_array no just the search of all.

      $search_all = $this -> get_articles_array_country($category_array, $country_id);

      $articles_array_country = [];
      foreach ($search_all as $number_packages => $obj) {
        foreach ($search_all[$number_packages]["results"] as $key => $value) {

          if ($return_object == true) {
            $articles_array_country[] = $search_all[$number_packages]["results"][$key];
          }
          else{
            $articles_array_country[] = $search_all[$number_packages]["results"][$key]["id"];
          }                           
        } 
      }

      return $articles_array_country;
    }

    public function get_articles_array_country($category_array, $country_id){

      // Pendiente cambiar el nombre get_articles_array_country por search_articles_array_country ya que devuelve la busqueda de todos los items, pero no los items en sí.
      //1- cambiar todos las llamadas y el nombre de esta funcion.
      //2- modificar esta funcion para que devuelva los items en sí
      foreach ($category_array as $key) {
            $url_array[] = "https://api.mercadolibre.com/sites/" . $country_id . "/search?category=" . $key;
        }

        //var_dump($url_array);

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;
    }


    public function get_full_article($item_id)
    {
        //This second json format of article is long (a lot of "adequate" info)

        $url = self::$GET_FULL_ARTICLE_URL . $item_id;

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, $assoc = true);

        return $obj;
    }

    public function get_full_article_array($item_ids_array)
    {
        //This format extracts elements in parallel count = get_articles_total -> $ limit.
              
        foreach ($item_ids_array as $key) {
            $url_array[] = self::$GET_FULL_ARTICLE_URL . $key;
        }

        $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
        
        foreach ($json_array as $key => $value) {
            $obj[$key] = json_decode($json_array[$key], $assoc = true);
        }

        return $obj;
    }

    public function get_item_time_active_array($time_created_array, $time_parameter, $healt_array){

      var_dump($time_created_array);
      var_dump($healt_array);

      $today_time_array_format = analysis::today($exact_time = true);
      $today_time = $today_time_array_format['year'] . "-" . $today_time_array_format['mon'] . "-" . $today_time_array_format['mday'];

      $time_created_array_format_yyyy_mm_dd = analysis::format_yyyy_mm_dd($time_created_array);

      foreach ($time_created_array_format_yyyy_mm_dd as $key => $value) {
        $diff = abs(strtotime($today_time) - strtotime($time_created_array_format_yyyy_mm_dd[$key]));
        $years = $diff / (365*60*60*24);
        $months = $diff / (30*60*60*24);
        $days = $diff / (60*60*24);

        switch ($time_parameter) {
          case 'years':
            $item_time_active_array[$key] = intval(floor($years * $healt_array[$key]));
            break;
          case 'months':
            $item_time_active_array[$key] = intval(floor($months * $healt_array[$key]));
            break;
          case 'days':
            $item_time_active_array[$key] = intval(floor($days * $healt_array[$key]));
            break;           
          default:
           $item_time_active_array[$key] = 0;
            break;
        }
      }

      var_dump("get_item_time_active_array");
      var_dump($item_time_active_array);

      return $item_time_active_array;

    }      

    public function get_item_time_active_array_azure($time_created_array, $time_parameter, $healt_array, $url_azure_cloud){

      var_dump($time_created_array);
      var_dump($healt_array);

      foreach ($time_created_array as $key) {
          $url_array[] = $url_azure_cloud . "&time=" . $key . "&time_parameter=" . $time_parameter;
      }

      $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
      
      foreach ($json_array as $key => $value) {
          $obj[$key] = json_decode($json_array[$key], $assoc = true);
          $obj[$key] = floor($obj[$key] * $healt_array[$key]);
      }

      var_dump("get_item_time_active_array");
      var_dump($obj);

      return $obj;

    }

    public function get_sold_quantity_scraping_array($permalinks_array, $url_azure_cloud){

      foreach ($permalinks_array as $key) {
          $url_array[] = $url_azure_cloud . "&permalink=" . $key;
      }

      $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));    
      foreach ($json_array as $key => $value) {
          $obj[$key] = json_decode($json_array[$key], $assoc = true);
      }

      return $obj;  
    }

    public function get_sold_quantity_array($object_array, $url_azure_cloud){

      //pending take difference between items that are greater and less than 4 sales
      $count_scraping_calls = 0;

      foreach ($object_array as $key => $value) {
        if ($object_array[$key]["sold_quantity"] < 5) {
          $sold_quantity_array[$key] = $object_array[$key]["sold_quantity"];
        }
        else {
          
          $save_position[$count_scraping_calls] = $key;
          $permalinks_array[$count_scraping_calls] = $object_array[$key]["permalink"];
          $count_scraping_calls = $count_scraping_calls + 1;

          $sold_quantity_array[$key] = $object_array[$key]["sold_quantity"];
        }
      }
      var_dump("sold_quantity_array");
      var_dump($sold_quantity_array);

      var_dump("permalinks_array");
      var_dump($permalinks_array);

      if ($permalinks_array != null) {
        
        //Pendiente estructurar en llamadas de a 50
        /*
        $limit_calls = 10;

        if (count($permalinks_array) > $limit_calls) {
            //var_dump("request_multiple more than " . $limit_calls);

            $total_calls = count($permalinks_array);

            $portions_urls = $permalinks_array;

            //var_dump("before portions_request_multiple count");
            //var_dump($total_calls);

            unset($permalinks_array);

            
            for ($j=1;  $j <= ceil($total_calls / $limit_calls); $j++)
            {
                //var_dump("count");
                //var_dump($j);

                foreach ($portions_urls as $key => $value) {
                    if ($j == ceil($total_calls / $limit_calls) && ($key == $total_calls % $limit_calls)){
                        break;
                    }
                    if ($key >= $limit_calls) {
                        
                        break;
                    }
                    $permalinks_array[$key] = $portions_urls[$key + ($j-1) * $limit_calls];
                }

                $portions_request_multiple = $this -> get_sold_quantity_scraping_array($permalinks_array, $url_azure_cloud);

                unset($permalinks_array);

                if($sold_quantity_scraping_array != null){
                    foreach ($portions_request_multiple as $key => $value) {
                        array_push($sold_quantity_scraping_array, $portions_request_multiple[$key]);
                    }
                }
                else{
                    $sold_quantity_scraping_array = $portions_request_multiple;
                }
            }         
        }
        else{
            $sold_quantity_scraping_array = $this -> get_sold_quantity_scraping_array($permalinks_array, $url_azure_cloud);
        }
        */

        $sold_quantity_scraping_array = $this -> get_sold_quantity_scraping_array($permalinks_array, $url_azure_cloud);

        var_dump("sold_quantity_scraping_array");
        var_dump($sold_quantity_scraping_array);

        foreach ($sold_quantity_scraping_array as $key => $value) {

          if ($sold_quantity_scraping_array[$key] != null) {          
           $step = $save_position[$key]; 
           $sold_quantity_array[$step] = $sold_quantity_scraping_array[$key];
          }
        }
      } 

      return $sold_quantity_array;
    }

    public function string_attribute($object_array){

      foreach ($object_array as $key => $value) {

          $string_attribute = "";  
          foreach ($object_array[$key]["attributes"] as $attribute => $value) {

            if ($object_array[$key]["attributes"][$attribute]["value_name"] != null) {

               $value_name_portion = explode(" ", $object_array[$key]["attributes"][$attribute]["value_name"]);

               $value_name = implode("_", $value_name_portion);

               $portion = $object_array[$key]["attributes"][$attribute]["id"] . "_" . $value_name;

               if ($string_attribute == "") {
                 $string_attribute = $portion;
               }
               else{
                $string_attribute = $string_attribute . " " .  $portion;
               }             
            }                       
          }

          $attributes_array[$key] = $string_attribute;
        }

        return $attributes_array; 
    }

    public function seller_object_array($object_array){

      foreach ($object_array as $key => $value) {      
        $seller_id_array[$key] = $object_array[$key]['seller_id'];
      }

       
      $seller_object = analysis::get_seller($seller_id_array);

      return $seller_object;
    }

    public function vendor_reputation_array($object_array, $want_integer = true){

     $seller_object = $this -> seller_object_array($object_array);

     foreach ($seller_object as $key => $value) {
      $reputation_array[$key] = $seller_object[$key]['seller_reputation']['level_id'];

      if($want_integer == true){

        $portions = explode("_", $reputation_array[$key]);
        $reputation_array[$key] = intval($portions[0]);
      }
     }

     return $reputation_array;
    }

    //predict market functions

    public function get_items_features($object_array, $features_to_obtain, $country_id = "MLM", $coin = "MXN"){

      //pendiente completar $features_to_obtain como array de entrada
      //always do
        foreach ($object_array as $key => $value) {
          $item_ids_array[$key] = $object_array[$key]["id"];
        }
        
      //title
        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["title"] = $object_array[$key]["title"];
        }

      //site_id
        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["site_id"] = $object_array[$key]["site_id"];
        }

      //price

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

        // $coin is the price reference, equal to $name_and_coin[$name]

        $currency_exchange_array = analysis::get_today_currency_exchange($coin, $name_and_coin);
        var_dump("currency_exchange_array");
        var_dump($currency_exchange_array);

        foreach ($object_array as $key => $value) {

           $get_items_match_array[$key]["price"] = round($object_array[$key]["price"] * $currency_exchange_array[$country_id], 2);
        }

        //var_dump("testing price");
         //var_dump($country_id);
         //var_dump($object_array[0]["price"]);
         //var_dump($currency_exchange_array[$country_id]);
         //var_dump($get_items_match_array[0]["price"]);

      //reputation vendor
        $reputation_vendor_array = $this -> vendor_reputation_array($object_array, $want_integer = true);
        foreach ($object_array as $key => $value) {
          if ($reputation_vendor_array[$key] === null) {
            $reputation_vendor_array[$key] = ""; 
          }
          $get_items_match_array[$key]["reputation_vendor"] = $reputation_vendor_array[$key];
        }

      //vendor_sales_completed
        $seller_object = $this -> seller_object_array($object_array);
        foreach ($object_array as $key => $value) {
          if ($seller_object[$key]["seller_reputation"]["transactions"]["completed"] === null) {
            $seller_object[$key]["seller_reputation"]["transactions"]["completed"] = ""; 
          }
          $get_items_match_array[$key]["vendor_sales_completed"] = $seller_object[$key]["seller_reputation"]["transactions"]["completed"];
        }

      //logistic_type
        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["logistic_type"] = $object_array[$key]["shipping"]["logistic_type"];
        }

      //free_shipping
        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["free_shipping"] = $object_array[$key]["shipping"]["free_shipping"];
        }
      
      //ranking
        $ranking_item_array = $this -> get_ranking_item_array($object_array, $limit = 50, $country_id);
        foreach ($object_array as $key => $value) {

          //Pendiente desactivar esta caracteristica y ver porque salen esos resultados null, 18 de enero no se entiende el por que salen null se asume error de MELI
          if ($ranking_item_array[$key] === null) {
            $ranking_item_array[$key] = ""; 
          }
          $get_items_match_array[$key]["ranking"] = $ranking_item_array[$key];
        }

      //conversion
      
          //sold_quantity
            $sold_quantity_array = $this -> get_sold_quantity_array($object_array, $url_azure_cloud = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");
            var_dump('sold_quantity_array');
            var_dump($sold_quantity_array);

          //views
            $views_array = analysis::total_item_visions_array($item_ids_array);
            var_dump('views_array');
            var_dump($views_array);

        foreach ($object_array as $key => $value) {
          if ($sold_quantity_array[$key] === null) {
            $get_items_match_array[$key]["conversion"] = ""; 
            continue;
          }
          else{
            $var1 = $sold_quantity_array[$key];
          }  
          
          $id = $object_array[$key]["id"];
          if ($views_array[$key][$id] === null) {
            $get_items_match_array[$key]["conversion"] = ""; 
            continue;
          }
          else{
            $var2 = $views_array[$key][$id];
          }
          
          $get_items_match_array[$key]["conversion"] = $var1/$var2;

          //Pendiente pasar a funcion covertir a 0 float
          
          if ((is_nan($get_items_match_array[$key]["conversion"])) || (is_infinite($get_items_match_array[$key]["conversion"])) || ($get_items_match_array[$key]["conversion"] === 0)) {
            $get_items_match_array[$key]["conversion"] = 0;
            $get_items_match_array[$key]["conversion"] = floatval($get_items_match_array[$key]["conversion"]);
          }
          

          $get_items_match_array[$key]["conversion"] = floatval($get_items_match_array[$key]["conversion"]);
          //var_dump('conversion');
          //var_dump($get_items_match_array[$key]["conversion"]);
        }  

      //condition
        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["condition"] = $object_array[$key]["condition"];
        }

      //catalog_product_id   

      foreach ($object_array as $key => $value) {
        if (isset($object_array[$key]["catalog_product_id"])){
          $get_items_match_array[$key]["catalog_product"] = true;
        }
        else{
          $get_items_match_array[$key]["catalog_product"] = false;
        }       
      }   

      //video_id
        foreach ($object_array as $key => $value) {
          if (isset($object_array[$key]["video"])){
            $get_items_match_array[$key]["video"] = true;
          }
          else{
            $get_items_match_array[$key]["video"] = false;
          }       
        }

      //accept_mercadopago

        foreach ($object_array as $key => $value) {
           $get_items_match_array[$key]["accepts_mercadopago"] = $object_array[$key]["accepts_mercadopago"];
        }

      //tags

        foreach ($object_array as $key => $value) {

        $separated_by_commas = implode(" ", $object_array[$key]["tags"]);
        $get_items_match_array[$key]["tags"] = $separated_by_commas;     
        }    

      //number of picture

        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["num_pictures"] = count($object_array[$key]["pictures"]);
        } 

      //attributes

        $attributes_array = $this -> string_attribute($object_array);

        foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["attributes"] = $attributes_array[$key];
         } 

      var_dump("attributes");  
      var_dump($attributes_array);    

      // reviews stars

      $reviews_object = analysis::opinions_item_array($item_ids_array);

      foreach ($object_array as $key => $value) {
        $get_items_match_array[$key]["reviews_average"] = $reviews_object[$key]["rating_average"];
      }

      // reviews number

      foreach ($object_array as $key => $value) {
        $get_items_match_array[$key]["reviews_total"] = $reviews_object[$key]["paging"]["total"];
      }

      //official_store_id

      foreach ($object_array as $key => $value) {
        if (is_null($object_array[$key]["official_store_id"])){
          $get_items_match_array[$key]["official_store"] = false;
        }
        else{
          $get_items_match_array[$key]["official_store"] = true;
        }       
      }

      //deal ids

      foreach ($object_array as $key => $value) {
        if (empty($object_array[$key]["deal_ids"])){
          $get_items_match_array[$key]["deal_ids"] = false;
        }
        else{
          $get_items_match_array[$key]["deal_ids"] = true;
        }       
      }

      //warranty

      foreach ($object_array as $key => $value) {
        if (is_null($object_array[$key]["warranty"])){
          $get_items_match_array[$key]["warranty"] = false;
        }
        else{
          $get_items_match_array[$key]["warranty"] = true;
        }       
      }

      //listing_type_id

        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["listing_type_id"] = $object_array[$key]["listing_type_id"];
        }

      //sold_quantity_for_days

        // item_days_active
        //Pendiente valorar si es necesario hacer esta busqueda de nuevo
        /*
        if (($object_array[0]["date_created"] === null) || ($object_array[0]["health"] === null)) {
          $result = $this -> get_full_article_array($item_ids_array);

          foreach ($result as $key => $value) {
            $time_created_array[$key] = $result[$key]["date_created"];
            $healt_array[$key] = $result[$key]["health"];
          }

          $url_azure_cloud_time_created = "https://predictmarket.azurewebsites.net/api/itemTimecreated?code=lDYeIva/V1NnW/M8lkBeiGnpZ/OyOp53hSa9WxY5WMLGnIlpIabH1w==";

          $item_time_active_array = $this -> get_item_time_active_array_azure($time_created_array, "days", $healt_array, $url_azure_cloud_time_created);
        }
        else{
          foreach ($object_array as $key => $value) {
            $time_created_array[$key] = $object_array[$key]["date_created"];
            $healt_array[$key] = $object_array[$key]["health"];
          }

          $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);
        } 
        */

        foreach ($object_array as $key => $value) {
          $time_created_array[$key] = $object_array[$key]["date_created"];
          $healt_array[$key] = $object_array[$key]["health"];
        }

        $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);     

        //sold_quantity
        if ($sold_quantity_array === null) {
          $sold_quantity_array = $this -> get_sold_quantity_array($object_array, $url_azure_cloud = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");
          var_dump('sold_quantity_array');
          var_dump($sold_quantity_array);
        }

      foreach ($object_array as $key => $value) {
        if ($sold_quantity_array[$key] === null) {
          $get_items_match_array[$key]["sold_quantity_for_days"] = ""; 
          continue;
        }
        else{
          $var1 = $sold_quantity_array[$key];
        }

        if ($item_time_active_array[$key] === null) {
          $get_items_match_array[$key]["sold_quantity_for_days"] = ""; 
          continue;
        }
        else{
          $var2 = $item_time_active_array[$key];
        }
        
        $get_items_match_array[$key]["sold_quantity_for_days"] = $var1/$var2;

      //Pendiente pasar a funcion covertir a 0 float
        
        if ((is_nan($get_items_match_array[$key]["sold_quantity_for_days"])) || (is_infinite($get_items_match_array[$key]["sold_quantity_for_days"])) || ($get_items_match_array[$key]["sold_quantity_for_days"] === 0)) {
          $get_items_match_array[$key]["sold_quantity_for_days"] = 0;
          $get_items_match_array[$key]["sold_quantity_for_days"] = floatval($get_items_match_array[$key]["sold_quantity_for_days"]);
        }
        

        $get_items_match_array[$key]["sold_quantity_for_days"] = floatval($get_items_match_array[$key]["sold_quantity_for_days"]);

        //var_dump("sold_quantity_for_days");
        //var_dump($get_items_match_array[$key]["sold_quantity_for_days"]);
      } 

      // more than 1 year and half in market boolian 
      /*
        if($item_time_active_array == null){
          $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);
        }

        //var_dump("active time products in days");
        //var_dump($item_time_active_array);
        
        foreach ($object_array as $key => $value) {

          if ($item_time_active_array[$key] != null) {
            if($item_time_active_array[$key] > 456){
              $get_items_match_array[$key]["more_than_1.25year?"] = true;
            }
            else{ $get_items_match_array[$key]["more_than_1.25year?"] = false;}
          }       
        }
      
      //weeks visits (52 x)

        $items_days_visits_array = analysis::visits_item_ndays($item_ids_array, 365);
       
        $items_52_week_visits_array = $this -> get_week_visits($item_ids_array, $items_days_visits_array, 52);
             
        foreach ($item_ids_array as $key => $item_id) {

          for ($j=1;  $j <= 52; $j++){
            $week = "last_" . $j . "week";
            $get_items_match_array[$key][$week] = $items_52_week_visits_array[$item_id][$week];
          }          
        }
      */
      //return

      return $get_items_match_array; 
    }

    public function get_items_features_v2($object_array, $features_to_obtain, $country_id = "MLM", $coin = "MXN"){

      //difference vs v_1
      //complete $features_to_obtain

      //always do
        foreach ($object_array as $key => $value) {
          $item_ids_array[$key] = $object_array[$key]["id"];
        }
        
      //title
      
        if (in_array("title", $features_to_obtain)){
          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["title"] = $object_array[$key]["title"];
          }
        }

      //site_id

        if (in_array("site_id", $features_to_obtain)){
          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["site_id"] = $object_array[$key]["site_id"];
          }
        }

      //price

        if (in_array("price", $features_to_obtain)){
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

          // $coin is the price reference, equal to $name_and_coin[$name]

          $currency_exchange_array = analysis::get_today_currency_exchange($coin, $name_and_coin);
          var_dump("currency_exchange_array");
          var_dump($currency_exchange_array);

          foreach ($object_array as $key => $value) {

             $get_items_match_array[$key]["price"] = round($object_array[$key]["price"] * $currency_exchange_array[$country_id], 2);
          }

          //var_dump("testing price");
          //var_dump($country_id);
          //var_dump($object_array[0]["price"]);
          //var_dump($currency_exchange_array[$country_id]);
          //var_dump($get_items_match_array[0]["price"]);
        }

      //reputation_vendor
 
        if (in_array("reputation_vendor", $features_to_obtain)){

          $reputation_vendor_array = $this -> vendor_reputation_array($object_array, $want_integer = true);
          foreach ($object_array as $key => $value) {
            if ($reputation_vendor_array[$key] === null) {
              $reputation_vendor_array[$key] = ""; 
            }
            $get_items_match_array[$key]["reputation_vendor"] = $reputation_vendor_array[$key];
          }
        }

      //vendor_sales_completed

        if (in_array("vendor_sales_completed", $features_to_obtain)){

          $seller_object = $this -> seller_object_array($object_array);
          foreach ($object_array as $key => $value) {
            if ($seller_object[$key]["seller_reputation"]["transactions"]["completed"] === null) {
              $seller_object[$key]["seller_reputation"]["transactions"]["completed"] = ""; 
            }
            $get_items_match_array[$key]["vendor_sales_completed"] = $seller_object[$key]["seller_reputation"]["transactions"]["completed"];
          }
        }

      //logistic_type

        if (in_array("logistic_type", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["logistic_type"] = $object_array[$key]["shipping"]["logistic_type"];
          }
        }

      //free_shipping

        if (in_array("free_shipping", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["free_shipping"] = $object_array[$key]["shipping"]["free_shipping"];
          }
        }

      //ranking

        if (in_array("ranking", $features_to_obtain)){

          $ranking_item_array = $this -> get_ranking_item_array($object_array, $limit = 50, $country_id);
          foreach ($object_array as $key => $value) {

            //Pendiente desactivar esta caracteristica y ver porque salen esos resultados null, 18 de enero no se entiende el por que salen null se asume error de MELI
            if ($ranking_item_array[$key] === null) {
              $ranking_item_array[$key] = ""; 
            }
            $get_items_match_array[$key]["ranking"] = $ranking_item_array[$key];
          }
        }

      //conversion

        if (in_array("conversion", $features_to_obtain)){
      
          //sold_quantity

            $sold_quantity_array = $this -> get_sold_quantity_array($object_array, $url_azure_cloud = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");
            var_dump('sold_quantity_array');
            var_dump($sold_quantity_array);
                   
          //views
            
            $views_array = analysis::total_item_visions_array($item_ids_array);
            var_dump('views_array');
            var_dump($views_array); 

          foreach ($object_array as $key => $value) {
            if ($sold_quantity_array[$key] === null) {
              
              $get_items_match_array[$key]["conversion"] = ""; 

              if (in_array("sold_quantity", $features_to_obtain)){
                $get_items_match_array[$key]["sold_quantity"] = "";
              }

              continue;
            }
            else{

              $var1 = $sold_quantity_array[$key];

              if (in_array("sold_quantity", $features_to_obtain)){
                $get_items_match_array[$key]["sold_quantity"] = $sold_quantity_array[$key];
              }
            }  
            
            $id = $object_array[$key]["id"];
            if ($views_array[$key][$id] === null) {

              $get_items_match_array[$key]["conversion"] = "";

              if (in_array("views", $features_to_obtain)){ 
                $get_items_match_array[$key]["views"] = "";
              }
              continue;
            }
            else{
              $var2 = $views_array[$key][$id];

              if (in_array("views", $features_to_obtain)){ 
                $get_items_match_array[$key]["views"] = $views_array[$key][$id];
              }
            }
            
            $get_items_match_array[$key]["conversion"] = $var1/$var2;

            //Pendiente pasar a funcion covertir a 0 float
            
            if ((is_nan($get_items_match_array[$key]["conversion"])) || (is_infinite($get_items_match_array[$key]["conversion"])) || ($get_items_match_array[$key]["conversion"] === 0)) {
              $get_items_match_array[$key]["conversion"] = 0;
              $get_items_match_array[$key]["conversion"] = floatval($get_items_match_array[$key]["conversion"]);
            }
            
            $get_items_match_array[$key]["conversion"] = floatval($get_items_match_array[$key]["conversion"]);
            //var_dump('conversion');
            //var_dump($get_items_match_array[$key]["conversion"]);
          }  
        }

      //condition

        if (in_array("condition", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["condition"] = $object_array[$key]["condition"];
          }
        }

      //catalog_product 

        if (in_array("catalog_product_id", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            if (isset($object_array[$key]["catalog_product_id"])){
              $get_items_match_array[$key]["catalog_product"] = true;
            }
            else{
              $get_items_match_array[$key]["catalog_product"] = false;
            }       
          }   
        }

      //video

        if (in_array("video", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            if (isset($object_array[$key]["video"])){
              $get_items_match_array[$key]["video"] = true;
            }
            else{
              $get_items_match_array[$key]["video"] = false;
            }       
          }
        }

      //accepts_mercadopago

        if (in_array("accepts_mercadopago", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
             $get_items_match_array[$key]["accepts_mercadopago"] = $object_array[$key]["accepts_mercadopago"];
          }
        }

      //tags

        if (in_array("tags", $features_to_obtain)){

          foreach ($object_array as $key => $value) {

          $separated_by_commas = implode(" ", $object_array[$key]["tags"]);
          $get_items_match_array[$key]["tags"] = $separated_by_commas;     
          }    
        }

      //num_pictures

        if (in_array("num_pictures", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["num_pictures"] = count($object_array[$key]["pictures"]);
          } 
        }

      //attributes

        if (in_array("attributes", $features_to_obtain)){

          $attributes_array = $this -> string_attribute($object_array);

          foreach ($object_array as $key => $value) {
              $get_items_match_array[$key]["attributes"] = $attributes_array[$key];
           } 

          var_dump("attributes");  
          var_dump($attributes_array); 
        }   

      //reviews_average

      if (in_array("reviews_average", $features_to_obtain)){

        $reviews_object = analysis::opinions_item_array($item_ids_array);

        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["reviews_average"] = $reviews_object[$key]["rating_average"];
        }
      }

      //reviews_total

      if (in_array("reviews_total", $features_to_obtain)){

        foreach ($object_array as $key => $value) {
          $get_items_match_array[$key]["reviews_total"] = $reviews_object[$key]["paging"]["total"];
        }
      }

      //official_store

      if (in_array("official_store", $features_to_obtain)){

        foreach ($object_array as $key => $value) {
          if (is_null($object_array[$key]["official_store_id"])){
            $get_items_match_array[$key]["official_store"] = false;
          }
          else{
            $get_items_match_array[$key]["official_store"] = true;
          }       
        }
      }  
      
      //deal_ids

      if (in_array("deal_ids", $features_to_obtain)){

        foreach ($object_array as $key => $value) {
          if (empty($object_array[$key]["deal_ids"])){
            $get_items_match_array[$key]["deal_ids"] = false;
          }
          else{
            $get_items_match_array[$key]["deal_ids"] = true;
          }       
        }
      }

      //warranty

      if (in_array("warranty", $features_to_obtain)){

        foreach ($object_array as $key => $value) {
          if (is_null($object_array[$key]["warranty"])){
            $get_items_match_array[$key]["warranty"] = false;
          }
          else{
            $get_items_match_array[$key]["warranty"] = true;
          }       
        }
      }
      //listing_type_id

        if (in_array("listing_type_id", $features_to_obtain)){

          foreach ($object_array as $key => $value) {
            $get_items_match_array[$key]["listing_type_id"] = $object_array[$key]["listing_type_id"];
          }
        }

      //sold_quantity_for_days

      if (in_array("sold_quantity_for_days", $features_to_obtain)){

        foreach ($object_array as $key => $value) {
          $time_created_array[$key] = $object_array[$key]["date_created"];
          $healt_array[$key] = $object_array[$key]["health"];
        }

        $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);     

        //sold_quantity
        if ($sold_quantity_array === null) {
          $sold_quantity_array = $this -> get_sold_quantity_array($object_array, $url_azure_cloud = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");
          var_dump('sold_quantity_array');
          var_dump($sold_quantity_array);
        }

      foreach ($object_array as $key => $value) {

        if ($sold_quantity_array[$key] === null) {

          $get_items_match_array[$key]["sold_quantity_for_days"] = ""; 

          if (in_array("sold_quantity", $features_to_obtain) && (!isset($get_items_match_array[$key]["sold_quantity"]))){
            var_dump("repeat_sold_quantity_search");
            $get_items_match_array[$key]["sold_quantity"] = "";
          }
          continue;
        }
        else{

          $var1 = $sold_quantity_array[$key];

          if (in_array("sold_quantity", $features_to_obtain) && (!isset($get_items_match_array[$key]["sold_quantity"]))){
            var_dump("repeat_sold_quantity_search");
            $get_items_match_array[$key]["sold_quantity"] = $sold_quantity_array[$key];
          }
        }

        if ($item_time_active_array[$key] === null) {

          $get_items_match_array[$key]["sold_quantity_for_days"] = ""; 

          if (in_array("item_days_active", $features_to_obtain)){
           $get_items_match_array[$key]["item_days_active"] = ""; 
          }
          continue;
        }
        else{

          $var2 = $item_time_active_array[$key];

          if (in_array("item_days_active", $features_to_obtain)){
           $get_items_match_array[$key]["item_days_active"] = $item_time_active_array[$key]; 
          }
        }
        
        $get_items_match_array[$key]["sold_quantity_for_days"] = $var1/$var2;
        
        if ((is_nan($get_items_match_array[$key]["sold_quantity_for_days"])) || (is_infinite($get_items_match_array[$key]["sold_quantity_for_days"])) || ($get_items_match_array[$key]["sold_quantity_for_days"] === 0)) {
          $get_items_match_array[$key]["sold_quantity_for_days"] = 0;
          $get_items_match_array[$key]["sold_quantity_for_days"] = floatval($get_items_match_array[$key]["sold_quantity_for_days"]);
        }
        
        $get_items_match_array[$key]["sold_quantity_for_days"] = floatval($get_items_match_array[$key]["sold_quantity_for_days"]);

        //var_dump("sold_quantity_for_days");
        //var_dump($get_items_match_array[$key]["sold_quantity_for_days"]);
       } 
      }

      return $get_items_match_array;
    }

    // pendiente borrar esta funcion "sobra" en clase meli

    public function fulfillment_calculator_item_id($item_id){

      $start_time_fulfillment_calculator_item_id = microtime(true);

      $item_object[0] = $this -> get_full_article($item_id);

      //get title

      $fulfillment_calculator[0]['title'] = $item_object[0]['title'];

      //logistic_type

      $fulfillment_calculator[0]["logistic_type"] = $item_object[0]["shipping"]["logistic_type"];

      //sold_quantity for days 

      foreach ($item_object as $key => $value) {
        $time_created_array[$key] = $item_object[$key]["date_created"];
        $healt_array[$key] = $item_object[$key]["health"];
      }

      $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);

      if ($sold_quantity_array === null) {
        $sold_quantity_array = $this -> get_sold_quantity_array($item_object, $url_azure_cloud = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");
        var_dump('sold_quantity_array');
        var_dump($sold_quantity_array);
      }

      foreach ($item_object as $key => $value) {
        if ($sold_quantity_array[$key] === null) {
          $fulfillment_calculator[$key]["sold_quantity_for_days"] = ""; 
          continue;
        }
        else{
          $var1 = $sold_quantity_array[$key];
        }

        if ($item_time_active_array[$key] === null) {
          $fulfillment_calculator[$key]["sold_quantity_for_days"] = ""; 
          continue;
        }
        else{
          $var2 = $item_time_active_array[$key];
        }
        
        $fulfillment_calculator[$key]["sold_quantity_for_days"] = $var1/$var2;

      //Pendiente pasar a funcion covertir a 0 float
        
        if ((is_nan($fulfillment_calculator[$key]["sold_quantity_for_days"])) || (is_infinite($fulfillment_calculator[$key]["sold_quantity_for_days"])) || ($fulfillment_calculator[$key]["sold_quantity_for_days"] === 0)) {
          $fulfillment_calculator[$key]["sold_quantity_for_days"] = 0;
          $fulfillment_calculator[$key]["sold_quantity_for_days"] = floatval($fulfillment_calculator[$key]["sold_quantity_for_days"]);
        }
        

        $fulfillment_calculator[$key]["sold_quantity_for_days"] = floatval($fulfillment_calculator[$key]["sold_quantity_for_days"]);

        //var_dump("sold_quantity_for_days");
        //var_dump($get_items_match_array[$key]["sold_quantity_for_days"]);
      }

      //new logistic_type

      $fulfillment_calculator[0]["new_logistic_type"] = "fulfillment";

      // predictive_fulfillment_sold_quantity_for_days
        
      foreach ($item_object as $key => $value) {

        $match = 0;
        if ($fulfillment_calculator[$key]["logistic_type"] == "fulfillment") {
            $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] =  $fulfillment_calculator[$key]["sold_quantity_for_days"];
        } else { 
           $prepare_to_predictive[$match] = $item_object[$key]; 
           $prepare_to_predictive[$match]["shipping"]["logistic_type"] = "fulfillment";
           $match = $match + 1; 

           /*
           $prepare_to_predictive[0] = $item_object[$key];

           $predictive_sold_quantity_for_days_array = $this -> call_mercadolibre_predictor($prepare_to_predictive, $features_to_obtain = null, $country_id = "MLM", $coin = "MXN");

           $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] = $predictive_sold_quantity_for_days_array[0]; 
          */ 
        }    

        $predictive_sold_quantity_for_days_array = $this -> call_mercadolibre_predictor($prepare_to_predictive, $features_to_obtain = null, $country_id = "MLM", $coin = "MXN");

        var_dump("predictive_sold_quantity_for_days_array");
        var_dump($predictive_sold_quantity_for_days_array);

        //complete holds

        $hold_number = 0;
        foreach ($item_object as $key => $value) {

          if ($fulfillment_calculator[$key]["logistic_type"] == "fulfillment") {
            continue;
          }
          else{

            if ($predictive_sold_quantity_for_days_array[$hold_number]['prediccion'] === null) {

              $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] = $predictive_sold_quantity_for_days_array[$hold_number]['prediccion'];

                          
            }
            else {

              $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] = floatval($predictive_sold_quantity_for_days_array[$hold_number]['prediccion']);

            }

            $hold_number = $hold_number + 1;

          }
        }              
      } 

     //save cookie result

     var_dump($fulfillment_calculator);

     $cookie_fulfillment_calculator = $fulfillment_calculator[0];

     $cookie_fulfillment_calculator["item_id"] = $item_object[0]["id"];

     var_dump("cookie_fulfillment_calculator");
     var_dump($cookie_fulfillment_calculator);
     
     $this -> cookie_fulfillment_calculator_item_id($cookie_fulfillment_calculator);



     //return result

     $end_time_fulfillment_calculator_item_id = microtime(true);
     echo "<br> Rounded runtime : fulfillment_calculator_item_id -> " . round($end_time_fulfillment_calculator_item_id - $start_time_fulfillment_calculator_item_id, 4) . " seconds";

     return $fulfillment_calculator;

    }

    public function fulfillment_calculator_seller_id($seller_id){

        $seller_articles_total = $this -> get_seller_articles_total_v2($seller_id, $plus = null, $country = 'MLM');

        //get title

            foreach ($seller_articles_total as $key => $value) {
               $fulfillment_calculator[$key]['title'] = $seller_articles_total[$key]['title'];
            }

        //logistic_type

            foreach ($seller_articles_total as $key => $value) {
              $fulfillment_calculator[$key]["logistic_type"] = $seller_articles_total[$key]["shipping"]["logistic_type"];
            }

        //sold_quantity for days

            if ($sold_quantity_array === null) {
              $sold_quantity_array = $this -> get_sold_quantity_array($seller_articles_total, $url_azure_cloud = "https://tablesoldquantity.azurewebsites.net/api/ScrapingHTTP?code=9TtiE9aIhq1apfSMbolmwZZgXOcyBXYFos0Zdn1zga2v/gaddrDBqw==");
              var_dump('sold_quantity_array');
              var_dump($sold_quantity_array);
            }

          foreach ($seller_articles_total as $key => $value) {
            if ($sold_quantity_array[$key] === null) {
              $fulfillment_calculator[$key]["sold_quantity_for_days"] = ""; 
              continue;
            }
            else{
              $var1 = $sold_quantity_array[$key];
            }

            if ($item_time_active_array[$key] === null) {
              $fulfillment_calculator[$key]["sold_quantity_for_days"] = ""; 
              continue;
            }
            else{
              $var2 = $item_time_active_array[$key];
            }
            
            $fulfillment_calculator[$key]["sold_quantity_for_days"] = $var1/$var2;

          //Pendiente pasar a funcion covertir a 0 float
            
            if ((is_nan($fulfillment_calculator[$key]["sold_quantity_for_days"])) || (is_infinite($fulfillment_calculator[$key]["sold_quantity_for_days"])) || ($fulfillment_calculator[$key]["sold_quantity_for_days"] === 0)) {
              $fulfillment_calculator[$key]["sold_quantity_for_days"] = 0;
              $fulfillment_calculator[$key]["sold_quantity_for_days"] = floatval($fulfillment_calculator[$key]["sold_quantity_for_days"]);
            }
            

            $fulfillment_calculator[$key]["sold_quantity_for_days"] = floatval($fulfillment_calculator[$key]["sold_quantity_for_days"]);

            //var_dump("sold_quantity_for_days");
            //var_dump($get_items_match_array[$key]["sold_quantity_for_days"]);
          }

        //new logistic_type

            foreach ($seller_articles_total as $key => $value) {
              $fulfillment_calculator[$key]["new_logistic_type"] = "fulfillment";
            }

        // fulfillment_sold_quantity_for_days
        
            foreach ($seller_articles_total as $key => $value) {

              $match = 0;
              if ($fulfillment_calculator[$key]["logistic_type"] == "fulfillment") {
                  $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] =  $fulfillment_calculator[$key]["sold_quantity_for_days"];
              } else { 
                 $prepare_to_predictive[$match] = $seller_articles_total[$key]; 
                 $match = $match + 1; 

                 /*
                 $prepare_to_predictive[0] = $seller_articles_total[$key];

                 $predictive_sold_quantity_for_days_array = $this -> call_mercadolibre_predictor($prepare_to_predictive, $features_to_obtain = null, $country_id = "MLM", $coin = "MXN");

                 $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] = $predictive_sold_quantity_for_days_array[0]; 
                */ 
              }    

             // $predictive_sold_quantity_for_days_array = $this -> call_mercadolibre_predictor($prepare_to_predictive, $features_to_obtain = null, $country_id = "MLM", $coin = "MXN");

              //complete holds

              $hold_number = 0;
              foreach ($seller_articles_total as $key => $value) {

                if ($fulfillment_calculator[$key]["logistic_type"] == "fulfillment") {
                  continue;
                }
                else{

                  $fulfillment_calculator[$key]["fulfillment_sold_quantity_for_days"] = $predictive_sold_quantity_for_days_array[$hold_number];
                  $hold_number = $hold_number + 1;
                }
              }              
            } 

        //return result

        return $fulfillment_calculator;     

    }

    public function call_mercadolibre_predictor($object_array, $features_to_obtain, $country_id = "MLM", $coin = "MXN"){

        $items = $this -> get_items_features($object_array, $features_to_obtain = null, $country_id);

        foreach ($items as $key => $value) {

          $items_input[0] = $items[$key]; // [0] "input_style" fix to send to api

          // change boolean to string to "input_style" fix to send to api

          $items_input[0]["free_shipping"]=$items_input[0]["free_shipping"] ? 'true' : 'false';
          $items_input[0]["catalog_product"]=$items_input[0]["catalog_product"] ? 'true' : 'false';
          $items_input[0]["video"]=$items_input[0]["video"] ? 'true' : 'false';
          $items_input[0]["accepts_mercadopago"]=$items_input[0]["accepts_mercadopago"] ? 'true' : 'false';
          $items_input[0]["official_store"]=$items_input[0]["official_store"] ? 'true' : 'false';
          $items_input[0]["deal_ids"]=$items_input[0]["deal_ids"] ? 'true' : 'false';
          $items_input[0]["warranty"]=$items_input[0]["warranty"] ? 'true' : 'false';
          
          $predictive_response = $this -> post_azure_machinelearning_predictive_request_response($items_input);

          var_dump("predictive_response");
          var_dump($predictive_response);

          $sold_quantity_for_days_array[$key] = $predictive_response;
        }

        return $sold_quantity_for_days_array;
    }

    public function post_azure_machinelearning_predictive_request_response($predictive_item_format){

      $body = array(
        "items" => $predictive_item_format
      );

      $url_api_mercadolibre_predictor = "https://prod-29.centralus.logic.azure.com:443/workflows/3156a3886fef4718bce4c07fc1c578fb/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=ab6P1XcOHPKZtvLWxFT8XCXi-1l1kAItRkOy9oMQpHk";

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
          CURLOPT_CONNECTTIMEOUT => 180, 
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_TIMEOUT => 180
      );

      $opts = array(
          CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
          CURLOPT_POST => true, 
          CURLOPT_POSTFIELDS => $body
      );

      $uri = $url_api_mercadolibre_predictor;

      $ch = curl_init($uri);
      curl_setopt_array($ch, $CURL_OPTS);
      curl_setopt_array($ch, $opts);

      $curl_response["body"] = json_decode(curl_exec($ch), true);
      $curl_response["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

      curl_close($ch);

      var_dump($curl_response);

      return $curl_response["body"];
    }

    public function cookie_fulfillment_calculator_item_id($fulfillment_calculator){

      $body = $fulfillment_calculator;

      $url_api_cookie_fulfillment_calculator__item_id = "https://meliappscesar.azurewebsites.net/api/cookie_fulfillment_calculator__item_id?code=relztT8alohFZZrvHnrrbwW1xO7OISmr08apEC9SyrSdn3dT4XMJtg==";

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
          CURLOPT_CONNECTTIMEOUT => 180, 
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_TIMEOUT => 180
      );

      $opts = array(
          CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
          CURLOPT_POST => true, 
          CURLOPT_POSTFIELDS => $body
      );

      $uri = $url_api_cookie_fulfillment_calculator__item_id;

      $ch = curl_init($uri);
      curl_setopt_array($ch, $CURL_OPTS);
      curl_setopt_array($ch, $opts);

      $return["body"] = json_decode(curl_exec($ch), true);
      $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

      curl_close($ch);

      var_dump($return);
    }

    //////////////// day_weight_model ////////////////////////

    public function get_and_storage_day_weight($min_children_category, $max_children_category, $country_base){

      //// Imcomplete ////////
      // obtein categories in country_base to pick data

        if ($country_base = "MLM") {
          $children_categories = $this -> get_all_children_categories_mexico_local();
        }
        else{
          return "just $country_base for MLM mexico";
        }

        var_dump($children_categories);

        $step = 0;
        for ($j = $min_children_category;  $j <= $max_children_category; $j++){

          $category_array[$step] = $children_categories[$j];
          $step = $step + 1;
        }

        var_dump($category_array);

      // search in categories witch items pass filter

        //pick categories with more than 100 items or call his father category

        $category_object = $this -> get_category_object($category_array); //could be improve using local search
        
        foreach ($category_object as $key => $value) {
         
         $category_object_more_1000[$key] = $category_object[$key];
         while ($category_object_more_1000[$key]["total_items_in_this_category"] < 1000) {

          //pick his father category
          $category_object_more_1000[$key] = $this -> get_father_object_local($category_object_more_1000[$key]["id"]); 

          var_dump("picked father category");
          var_dump($category_object_more_1000[$key]);
           
         }
        }  
        
        var_dump($category_object_more_1000);   

        //select items to pass filter

          foreach ($category_object_more_1000 as $key => $value) {

            $match_category = $category_object_more_1000[$key]["id"];
            $Tiny_data = false; // you want to prioritize that finds more data from the base country
            $array_to_search = $this -> get_articles_total_v2($match_category, $total_articles = 1000, $limit = 50, $country_base, $plus = null, $Tiny_data);

            $result_category_items[$match_category] = $this -> get_full_article_array($array_to_search);
          }

          var_dump(count($result_category_items));

          // pass filter more than 1.5 year active, 100% active and week_difference_in_wrap less than 50%
          //me quede aqui comprobar y testear
          foreach ($result_category_items as $category_id => $value) {

            $match = 0;

            foreach ($result_category_items[$category_id] as $item => $value) {

              // 1 filter "complete health"

              $healt_array[$item] = $result_category_items[$category_id][$item]["health"];

              // 2 filter "more than 1.5 year active"

              $time_created_array[$item] = $result_category_items[$category_id][$item]["date_created"];

              // 3 filter "week_difference_in_wrap less than 200%"

              $item_ids_array[$item] = $result_category_items[$category_id][$item]["id"];

            }

            $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);

            $items_days_visits_array = analysis::visits_item_ndays($item_ids_array, 365);

            $items_52_week_visits_array = $this -> get_week_visits($item_ids_array, $items_days_visits_array, 52);

            //evaluation of filters

            foreach ($result_category_items[$category_id] as $item => $value) 
            {
              $item_id = $result_category_items[$category_id][$item]["id"];

              $week_difference_in_wrap[$item] = $items_52_week_visits_array[$item_id]['last_1week'] - 
              $items_52_week_visits_array[$item_id]['last_52week'];

              $week_difference_in_wrap_percent[$item] = $week_difference_in_wrap[$item]/$items_52_week_visits_array[$item_id]['last_52week'];

              if (($item_time_active_array[$item] >= 365) && ($healt_array[$item] == 1) && ($week_difference_in_wrap_percent[$item] <= 1)) {

                var_dump("match found");

                if (count($item_id_sample[$category_id]) <= 100) {
                  $item_id_sample[$item] = $item_id;
                  //$item_id_sample[$category_id][$item_id] = $items_days_visits_array[$item_id];
                  $match = $match + 1;
                }
                else{
                  continue 2;
                }             
              }

            }

            var_dump("week_difference_in_wrap_percent");
            var_dump($week_difference_in_wrap_percent);

            $visits_body = analysis::visits_item_ndays_v2($item_id_sample);

            var_dump("visits_body");
            var_dump($visits_body);

            //analysis::post_portions_request_multiple($bodys, $urls, $opciones = array())
         }
    }

    public function get_and_storage_day_weight_v2($min_children_category, $max_children_category, $country_base)
    {
      // obtein categories in country_base to pick data

        if ($country_base = "MLM") {
          $fathers_of_children_categories = $this -> get_all_fathers_of_children_categories_mexico_local();
        }
        else{
          return "just $country_base for MLM mexico";
        }

        var_dump($fathers_of_children_categories);

        $step = 0;
        for ($j = $min_children_category;  $j <= $max_children_category; $j++){

          $category_array[$step] = $fathers_of_children_categories[$j];
          $step = $step + 1;
        }

        var_dump($category_array);

        $category_object = $this -> get_category_object($category_array); //could be improve using local search

        var_dump($category_object);

      //select items to pass filter  

        foreach ($category_object as $key => $value) {

          $match_category = $category_object[$key]["id"];
          $Tiny_data = false; // you want to prioritize that finds more data from the base country

          $fathers_category_array = array('0' => $match_category);

          $father_items = $this -> get_items_children_of_father_category($fathers_category_array, $total_max_items = 5000, $country_base, $force_less_than_1000 = true);

          $array_to_search[$key] = $father_items[0];

          var_dump($array_to_search);

          /*
          if ($category_object[$key]["total_items_in_this_category"] < 1000) {

             $array_to_search[$key] = $this -> get_articles_total_v2($match_category, $total_articles = null, $limit = 50, $country_base, $plus = null, $Tiny_data);
          } else {

            $array_to_search[$key] = $this -> get_articles_total_v2($match_category, $total_articles = 1000, $limit = 50, $country_base, $plus = null, $Tiny_data);
          }
          */

          // 3 filter "week_difference_in_wrap less than 200%"

          $items_days_visits_array = analysis::visits_item_ndays($array_to_search[$key], 365);

          $items_52_week_visits_array = $this -> get_week_visits($array_to_search[$key], $items_days_visits_array, 52);

          var_dump("items_52_week_visits_array");
          var_dump($items_52_week_visits_array);
          $index = 0;
          foreach ($array_to_search[$key] as $item => $value) 
          {

            $item_id = $array_to_search[$key][$item];

            $week_difference_in_wrap[$item] = $items_52_week_visits_array[$item_id]['last_1week'] - 
            $items_52_week_visits_array[$item_id]['last_52week'];

            if ($items_52_week_visits_array[$item_id]['last_52week'] == 0) {
              $week_difference_in_wrap_percent[$item] = 0;
            } else {
              $week_difference_in_wrap_percent[$item] = $week_difference_in_wrap[$item]/$items_52_week_visits_array[$item_id]['last_52week'];
            }
                     
            if (($week_difference_in_wrap_percent[$item] <= 1) && ($week_difference_in_wrap_percent[$item] != 0)){

              $first_filter_without_indexing[$match_category][$item] = $item_id; 
              $first_filter_indexing[$match_category][$index] = $item_id;
              $index = $index + 1;
            }
            else{
              continue;
            }
          }

          var_dump("it pass filter week_difference_in_wrap less than 200%");
          var_dump($first_filter_indexing);

          //$result_category_items[$match_category] = $this -> get_full_article_array($array_to_search);
        }

      // Prepare to send to Azure logic apps "storage_day_weight_metadata"
        $index = 0;
        foreach ($first_filter_indexing as $key => $value) {

          $visits_item_array[$index]["father_category_id"] = $key;
          $visits_item_array[$index][$key]= analysis::visits_item_ndays_v2($first_filter_indexing[$key]);
          
          // Pendiente agregar seguridad $key_vault
          $urls[$index] = "https://prod-16.centralus.logic.azure.com:443/workflows/9e7b998364864cb9bcb6d3dee0bcfcd1/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=8d4YXsc47jdiU-ewhGiDHu4Du6BMn2o5SC4NW1gCSBQ";
          $index = $index + 1;
        }

        var_dump("item with visits_item_ndays_v2");
        var_dump($visits_item_array);
        
        curl::post_portions_request_multiple($visits_item_array, $urls, $opciones = null);

    }

    public function process_day_weight_and_storage_v2($min_children_category, $max_children_category, $country_base){

      if ($country_base = "MLM") {
        $fathers_of_children_categories = $this -> get_all_fathers_of_children_categories_mexico_local();
      }
      else{
        return "just $country_base for MLM mexico";
      }

      var_dump($fathers_of_children_categories);

      $step = 0;
      for ($j = $min_children_category;  $j <= $max_children_category; $j++){

        $category_array[$step] = $fathers_of_children_categories[$j];
        $step = $step + 1;
      }

      var_dump($category_array);

      //receive blob to calculate_day_weight (this is for cases 2019-01-01 and 2020-01-01)
        
        foreach ($category_array as $key => $value) {
          $url_upload_day_visits_metadata[$key] = "https://dayvisitsweightmeli.azurewebsites.net/api/upload_day_visits_metadata?code=IQSGPHV95jkdcgEcefmD6WJihzLK1oxdOgDaR96JMHY22UcVB69G4A==&category_id=" . $category_array[$key];
        }

        $load_visits_item_string = curl::request_multiple($url_upload_day_visits_metadata, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        var_dump("load_visits_item_string");
        var_dump($load_visits_item_string);

        foreach ($load_visits_item_string as $key => $value) {
          $load_visits_item_array[$key] = json_decode($load_visits_item_string[$key], $assoc = true);
        }
        
        var_dump("load_visits_item_array");
        var_dump($load_visits_item_array);

     // calculate_day_weight  

        $visits_item_day_weight_array = $this -> calculate_day_weight($load_visits_item_array);

        var_dump("visits_item_day_weight_array");
        var_dump($visits_item_day_weight_array);

     // search for children categories

        foreach ($visits_item_day_weight_array as $key => $value) {
         $father_category_array[$key] = $visits_item_day_weight_array[$key]["father_category_id"];
        }

        $children_categories_array = $this -> get_children_array($father_category_array);

        var_dump("children_categories_array");
        var_dump($children_categories_array);

      // post to storage_day_weight
        $index = 0;
        foreach ($children_categories_array as $key => $value) {

          foreach ($children_categories_array[$key] as $children_index => $value) {

            $category_children = $children_categories_array[$key][$children_index];

            $union_category_children = array("children_category" => $category_children);
            $children_category_day_weight[$index] = array_merge($union_category_children, $visits_item_day_weight_array[$key]);

            //$children_category_day_weight[$index][$category_children]["children_category"] = $category_children;
            //$children_category_day_weight[$index][$category_children] = $visits_item_day_weight_array[$key];
            
            $url_upload_day_weight_blob[$index] = "https://dayvisitsweightmeli.azurewebsites.net/api/day_weight_to_blob?code=xLnFDc90V8HLabbwUn4ywJYw3nQUTEPeQDdjtNKzvvFzdDOOxR7Cyw==";

            $index = $index + 1;
          }
        }

        var_dump("ready to post");
        var_dump("children_category_day_weight");
        var_dump($children_category_day_weight);

        curl::post_portions_request_multiple($children_category_day_weight, $url_upload_day_weight_blob, $opciones = null);
    }

    public function get_items_children_of_father_category($fathers_category_array, $total_items, $country_base, $force_less_than_1000){

      $children_array = $this -> get_children_array($fathers_category_array);

      //var_dump("children_array");
      //var_dump($children_array);    

      if ($force_less_than_1000 == true) {
        $total_articles = 1000;
      } 
          
      foreach ($children_array as $key => $value) {
        
        $start_time = microtime(true);

        foreach ($children_array[$key] as $children_category_index => $value) {

          if (isset($item_father_category) && (count($item_father_category) > $total_items)) {
            break (1);
          }

          //var_dump("index");
          //var_dump($children_array[$key][$children_category_index]);

          //search children category items
          $children_category = $children_array[$key][$children_category_index];
          $add_item_category = $this -> get_articles_total_v2($children_category, $total_articles, $limit = 50, $country_base, $plus = null, $force_calculation_rand_under_1000 = false);

          //var_dump("children category");
          //var_dump($children_category);

          //var_dump("add_item_category");
          //var_dump($add_item_category);

          if(isset($item_father_category)){

            foreach ($add_item_category as $item_index => $value) {
              array_push($item_father_category, $add_item_category[$item_index]);
            }
          }
          else{
            $item_father_category = $add_item_category;
          } 
          unset($add_item_category);

          //var_dump("item_father_category");
          //var_dump($item_father_category);    
        }

        $item_father_category_array[$key] = $item_father_category;

        $end_time = microtime(true);

        echo "<br> Rounded runtime: get_items_children_of_father_category ->" . round($end_time - $start_time, 6) . " seconds";
        echo "<br>download speed : " . round(count($item_father_category_array[$key])/($end_time - $start_time), 4) . " art/seconds";
         
      }

      //var_dump("get_items_children_of_father_category");
      //var_dump($item_father_category_array);

      return $item_father_category_array;
      
    }

    public function calculate_day_weight($visits_item_array){

      foreach ($visits_item_array as $index => $value) {

        $father_category_id = $visits_item_array[$index]["father_category_id"];
        $day_weight[$index]["father_category_id"] = $visits_item_array[$index]["father_category_id"];
        foreach ($visits_item_array[$index][$father_category_id] as $item_id => $value) {
          
          
          foreach ($visits_item_array[$index][$father_category_id][$item_id] as $date_yyyy_mm_dd => $value) {

            //var_dump($date_yyyy_mm_dd);
            
            $date_mm_dd = analysis::date_mm_dd($date_yyyy_mm_dd);
            
            //sum in case of example : 2019_01_01 and 2020_01_01 it get ist average day views

            if (!isset($exact_day_views[$father_category_id][$item_id][$date_mm_dd])) {
              
               $exact_day_views[$father_category_id][$item_id][$date_mm_dd] = $visits_item_array[$index][$father_category_id][$item_id][$date_yyyy_mm_dd]["visits"];
            } else {
              
              $exact_day_views[$father_category_id][$item_id][$date_mm_dd] = ($exact_day_views[$father_category_id][$item_id][$date_mm_dd] + $visits_item_array[$index][$father_category_id][$item_id][$date_yyyy_mm_dd]["visits"])/2; //average 
            }

            $all_items_day_visits[$father_category_id][$date_mm_dd] =  $all_items_day_visits[$father_category_id][$date_mm_dd] + $exact_day_views[$father_category_id][$item_id][$date_mm_dd];

            //var_dump("all_items_day_visits");
            //var_dump($all_items_day_visits);

            $category_total_visits[$father_category_id] = $category_total_visits[$father_category_id] + $exact_day_views[$father_category_id][$item_id][$date_mm_dd]; 

            //var_dump("category_total_visits");
            //var_dump($category_total_visits);
                  
          }
        }
      }

    //calculate weight

      $index = 0;
      foreach ($all_items_day_visits as $father_category_id => $value) {
        
        foreach ($all_items_day_visits[$father_category_id] as $date_mm_dd => $value) {
          
          $day_weight[$index]["day_weight"][$date_mm_dd] = $all_items_day_visits[$father_category_id][$date_mm_dd]/$category_total_visits[$father_category_id]; 

          //var_dump("day_weight incomplete");
          //var_dump($day_weight);
        }
        $index = $index + 1;
      }    

      return $day_weight;
    }

    public function comparison_model_machine_learning_out_meli($min_children_category, $max_children_category,  $country_base = "MLM"){

      //this function load categories from azure blob storage, eliminate columns, and send it to azure machine learning

      //ist rase ranking and conversion

     // obtein categories in country_base to pick data

        if ($country_base = "MLM") {
          $fathers_of_children_categories = $this -> get_all_fathers_of_children_categories_mexico_local();
        }
        else{
          return "just $country_base for MLM mexico";
        }

        var_dump($fathers_of_children_categories);

        $step = 0;
        for ($j = $min_children_category;  $j <= $max_children_category; $j++){

          $father_category_array[$step] = $fathers_of_children_categories[$j];
          $step = $step + 1;
        }

        var_dump("father_category_array");
        var_dump($father_category_array);

        $father_children_array = $this -> get_children_array($father_category_array);

        $offset = 0;
        foreach ($father_children_array as $father_category => $value) {
          
          foreach ($father_children_array[$father_category] as $key => $value) {
            $category_array[$offset] = $father_children_array[$father_category][$key];
            $offset = $offset + 1;
          }
        }
        var_dump("children_categories input");
        var_dump($category_array);

        $children_data = $this -> get_blob_saves_children_categories_data($category_array);

        var_dump("children_data");
        var_dump($children_data);

        foreach ($children_data as $children_category => $value) {
          foreach ($children_data[$children_category] as $key => $value) {
            foreach ($children_data[$children_category][$key] as $attribute => $value) {
              
              if (($attribute == 'ranking') || ($attribute == 'conversion')) {
                //do nothing
              } else {
                $children_data_out_meli[$children_category][$key][$attribute] = $children_data[$children_category][$key][$attribute];
              }
            
            }
          }
        }

        //unify all children categories items in father category items
        // me quede aqui crear este unify para mandar data solo de categoria padre
        foreach ($father_children_array as $key => $value) {
          # code...
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

        var_dump("children_data_out_meli");
        var_dump($children_data_out_meli);

        foreach ($children_data_out_meli as $category_index => $value) {
          $this -> csv_to_machinelearning_v2_out_meli($children_data_out_meli[$category_index], $category_array[$category_index]);
        }
        


    }

    public function get_blob_saves_children_categories_data($children_category_array){

      foreach ($children_category_array as $key) {
          $url_array[] = "https://generaldatawithoutmelistandart.azurewebsites.net/api/blob_csv_to_json?code=r9OyYaz1o4jVeTP7m0LUc2N9sJXLkuaTY43pt78xJZBDulG7uTB0yw==" . "&category_id=" . $key;
      }

      $json_array = curl::request_multiple($url_array, $options = array(CURLOPT_SSL_VERIFYPEER => false));
      
      foreach ($json_array as $key => $value) {
          
          $children_data[$key] = json_decode($json_array[$key], $assoc = true);
      }

      return $children_data;
    }

    public function csv_to_machinelearning_v2_out_meli($items, $category_id){

      $Azure_logicApps_TrainingMachineLearning = "https://prod-19.centralus.logic.azure.com:443/workflows/96e3d6f34edc43be849ae7304c8fedf7/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=9rcOZAumI5eJy7kJEIK7ylJPIUkxN9LNNRnJAnogYZU";

      //$Azure_csvtoAzureBlob = "https://mlconexionmeli.azurewebsites.net/api/csvtoAzureBlob?code=xMNGNalSwHfaSpdkyfD/5lcvqftl8wBAfl7PKtX5qrQbyUsYmSqasQ==";
      
      $body = array(
        "items" => $items,
        "category_id" => $category_id
      );
      
      $save_category = $category_id;

      //$DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
      //$dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . 'features_predictor.json';

      //var_dump("dir_save");
      //var_dump($dir_save);

      //$this -> save_json($body, $dir_save);
      
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
          CURLOPT_CONNECTTIMEOUT => 180, 
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_TIMEOUT => 180
      );

      $opts = array(
          CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
          CURLOPT_POST => true, 
          CURLOPT_POSTFIELDS => $body
      );

      $uri = $Azure_logicApps_TrainingMachineLearning;

      $ch = curl_init($uri);
      curl_setopt_array($ch, $CURL_OPTS);
      curl_setopt_array($ch, $opts);

      $return["body"] = json_decode(curl_exec($ch), true);
      $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

      curl_close($ch);

      var_dump($return);
    }


    public function get_week_visits($item_ids_array, $items_days_visits_array, $number_of_weeks){

      //From the beginning of the day of the start of the calculation, e.g. starts the wednesday calculation 6 pm the first week, would be from Wednesday 00:00 am to Wednesday 00:00 am from Wednesday last week
      //eg. if ($number_of_weeks = 52) the algorithm won't consider $items_days_visits_array[0], because 52 weeks is not exactly one year.

      foreach ($item_ids_array as $key => $item_id) {

        $total_days = count($items_days_visits_array[$item_id]);
        $test_count = 0;
        for ($i = 0;  $i < $number_of_weeks; $i++)
        {
          $start_day = ($total_days - 1) - (7 * $i);
          $ends_day = ($total_days - 8) - (7 * $i);

          //var_dump("start_day and ends_day");
          //var_dump($start_day);
          //var_dump($ends_day);

          $week = "last_" . ($i + 1) . "week";
          $add_days_of_the_week = 0;
          
          for ($j = $start_day;  $j > $ends_day; $j--){

            //var_dump("days");
            //var_dump($j);

            $add_days_of_the_week = $add_days_of_the_week + $items_days_visits_array[$item_id][$j][1];
            //var_dump($add_days_of_the_week);
          }
          //var_dump("add_days_of_the_week" . $week);
          //var_dump($add_days_of_the_week);

          $test_count = $test_count + $add_days_of_the_week;

          $items_52_week_visits_array[$item_id][$week] = $add_days_of_the_week;
        }
        //var_dump("test total visits");
        //var_dump($test_count);
      }

      return $items_52_week_visits_array;
    }

    public function get_conversion_array($object_array, $url_azure_cloud){
      //sold_quantity
      $sold_quantity_array = $this -> get_sold_quantity_array($object_array, $url_azure_cloud);
      var_dump($sold_quantity_array);

      foreach ($object_array as $key => $value) {
        $item_id_array[$key] = $object_array[$key]["id"];
      }
      //views
      $views_array = analysis::total_item_visions_array($item_id_array);
      var_dump($views_array);
      // item_days_active
      foreach ($object_array as $key => $value) {
        $item_ids_array[$key] = $object_array[$key]["id"];
      }

      $result = $this -> get_full_article_array($item_ids_array);

      foreach ($result as $key => $value) {
        $time_created_array[$key] = $result[$key]["date_created"];
        $healt_array[$key] = $result[$key]["health"];
      }

      //Pendiente borrar url_azure_cloud_time_created, primero verificar que local la resta de dias funciona bien  
      $url_azure_cloud_time_created = "https://predictmarket.azurewebsites.net/api/itemTimecreated?code=lDYeIva/V1NnW/M8lkBeiGnpZ/OyOp53hSa9WxY5WMLGnIlpIabH1w==";

      $item_time_active_array = $this -> get_item_time_active_array($time_created_array, $time_parameter = "days", $healt_array);

      foreach ($object_array as $key => $value) {
        $var1 = $sold_quantity_array[$key];
        $id = $object_array[$key]["id"];
        $var2 = $views_array[$key][$id];
        $var3 = $var1/$var2;
        $conversion[$key] = $var3;
        var_dump($conversion[$key]);
      }

      foreach ($object_array as $key => $value) {

        $id = $object_array[$key]["id"];

        $conversion_parameters[$key]["0"] = $object_array[$key]["title"];
        $conversion_parameters[$key]["1"] = $sold_quantity_array[$key];
        $conversion_parameters[$key]["2"] = $views_array[$key][$id];
        $conversion_parameters[$key]["3"] = $item_time_active_array[$key];
        $conversion_parameters[$key]["4"] = $conversion[$key];


        echo "titulo : " . $conversion_parameters[$key]["0"] . "<br>";
        echo "vendidos : " . $conversion_parameters[$key]["1"] . "<br>";
        echo "visitas : " . $conversion_parameters[$key]["2"] . "<br>";
        echo "tiempo_activo : " . $conversion_parameters[$key]["3"] . " dias<br>";
        echo "conversion : " . $conversion_parameters[$key]["4"] . "<br><br><br>";
      }

      return $conversion_parameters;
    }

    public function get_seller_articles($seller_id_or_nickname = array(), $plus, $country = 'MLM'){

      $nickname = analysis::get_nickname($seller_id_or_nickname); 

      $nickname_search = analysis::separator_search($nickname);

       $url = "https://api.mercadolibre.com/sites/" . $country . self::$GET_SELLER_ARTICLES . $nickname_search . $plus;

       $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

       $obj = json_decode($json, $assoc = true);

       return $obj;
    }

    public function get_seller_articles_v2($seller_id, $plus, $country = 'MLM'){

      $url = "https://api.mercadolibre.com/sites/" . $country . "/search?seller_id=" . $seller_id . $plus;

       $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

       $obj = json_decode($json, $assoc = true);

       return $obj;
    }

    /*
      $options_work_and_save [work_and_save, get_articles_total]
        get_inactive = true //Take files that are inactive
        get_conversion = true //Answer an array with the conversion
            { // ADD to array in order to specify whitch sort you want.
              "more_conversion" => true
              "more_views" => true
              "more_solds" => true  
            }
        dont_save = true // It does not save the results on the computer, it only processes them
        dir_to_save = "string" // You need to enter a local address in the form of a string, force address to save 

    */

    /*
      $options_work[get_articles_total]
        get_expansion => true // Perform an expansive method to exceed 10k with an average effectiveness of increasing the amount of items by 40%.
        dont_pass_+1000 = true // It return null if there is more than 1000 items in $category input      
    */ 

    public function get_seller_articles_total($seller_id_or_nickname = array(), $plus, $country = 'MLM', $options_work_and_save, $category_offset = null){

      $start_time_get_seller_articles_total = microtime(true);

      $obj = $this->get_seller_articles($seller_id_or_nickname, $plus, $country);

      $total_articles = $obj['paging']['total'];
      var_dump(" This seller has a total articles: " .  $total_articles);

      $nickname = analysis::get_nickname($seller_id_or_nickname);

      $nickname_search = analysis::separator_search($nickname);

      $available_filters = $obj['available_filters'];

      $category_available_filters = analysis::available_filters($available_filters, $option = array("category" => true));

      foreach ($category_available_filters['values'] as $key => $value) {

        if ($category_offset != null && $category_available_filters['values'][$key]['id'] != $category_offset) {
          var_dump("category: " . $category_available_filters['values'][$key]['id'] . " skipped");
          continue;
        }

        //Pendiente valorar adicionar a la primera variable de get_articles_total una variable $plus para posibles filtros

        /* Here you can have two variants to save the information.*/
        // 1 - save a single json with all the items by category.
        // 2 - save for each category many json each one with an item.

        // selecting option number 1
        /*
        $this -> get_articles_total($category . "&nickname=" . $nickname_search, 50, $options_work_and_save = array("get_conversion" => true, "dir_to_save" => $dir_to_save, "more_solds" => true));
        */
        // selecting option number 2
        /*
        $this -> get_articles_total($category . "&nickname=" . $nickname_search, $limit = 50, $options_work_and_save = array("dir_to_save" => $dir_to_save), $options_work = null);
        */

        $category =  $category_available_filters['values'][$key]['id'];

        $this -> get_articles_total($category . "&nickname=" . $nickname_search, $limit = 50, $options_work_and_save);

        $this -> save_error($save = false);// $save=false It is equivalent to working errors

      }

      $end_time_get_seller_articles_total = microtime(true);
      echo "<br> Rounded runtime : get_seller_articles_total -> " . round($end_time_get_seller_articles_total - $start_time_get_seller_articles_total, 4) . " seconds";
      echo "<br>download speed : " . round($total_articles/($end_time_get_seller_articles_total - $start_time_get_seller_articles_total), 4) . " art/seconds";
    }

    public function get_seller_articles_total_v2($seller_id, $plus, $country = 'MLM', $category_offset = null){

      $start_time_get_seller_articles_total = microtime(true);

      $obj = $this -> get_seller_articles_v2($seller_id, $plus, $country);

      $total_articles_seller = $obj['paging']['total'];
      var_dump(" This seller has a total articles: " .  $total_articles_seller);

      //$nickname_search = analysis::separator_search($nickname);

      $available_filters = $obj['available_filters'];

      $category_available_filters = analysis::available_filters($available_filters, $option = array("category" => true));

      if ($category_available_filters == "It has not registered in the filters the characteristic category") {
          return "It has not registered in the filters the characteristic category";
      }

      foreach ($category_available_filters['values'] as $key => $value) {

        if ($category_offset != null && $category_available_filters['values'][$key]['id'] != $category_offset) {
          var_dump("category: " . $category_available_filters['values'][$key]['id'] . " skipped");
          continue;
        }

        $category =  $category_available_filters['values'][$key]['id'];

        //$this -> get_articles_total($category . "&nickname=" . $nickname_search, $limit = 50, $options_work_and_save);

        $array_to_search = $this -> get_articles_total_v2($category, $total_articles = null, $limit = 50, $country, $plus = "&seller_id=" . $seller_id, $force_calculation_under_1000 = false);

        $result_category_items = $this -> get_full_article_array($array_to_search);

        //push an array of competition items, ex: A + AB + ABC ...
        if($seller_articles_total != null){
          foreach ($result_category_items as $key => $value) {
            array_push($seller_articles_total, $result_category_items[$key]);
          }
        } 
        else{
          $seller_articles_total = $result_category_items;
        }

      }

      $end_time_get_seller_articles_total = microtime(true);
      echo "<br> Rounded runtime : get_seller_articles_total -> " . round($end_time_get_seller_articles_total - $start_time_get_seller_articles_total, 4) . " seconds";
      echo "<br>download speed : " . round($total_articles_seller/($end_time_get_seller_articles_total - $start_time_get_seller_articles_total), 4) . " art/seconds";

      return $seller_articles_total;
    }
        
    public function get_articles_total($category, $limit = 50, $options_work_and_save, $options_work = null)
    {
        $start_time = microtime(true);
        
        $obj = $this->get_articles($category);
       
        $total_articles = $obj["paging"]["total"];
        var_dump($category . " It has a total of articles: " .  $total_articles);

        var_dump($category . "&offset=0&limit=50"); //Its just for information of starting

        if ($total_articles > 1000) {
          
            if($options_work['dont_pass_+1000'] == true){
              return null;
            }
            else{
              $save_access_token = "&access_token=" . analysis::access_token();
              $access_token = null;
              $save_token_items = $total_articles;
              $total_articles = 1000;
              $pass_for_token = true;
              $plus_token = 0;
            }          
        } else {
            $access_token = null;
            $pass_for_token = false;
            $plus_token = 0;
        }


        token_process:   // later called to cycle for total_articles > 1000 using access_token.

       for ($j=1;  $j <= ceil($total_articles / $limit); $j++) {
           $i=0;
           
           while ($i < $limit) {
               if ($obj["results"][$i] == null) {
                   if ($j == ceil($total_articles / $limit) && $i == $total_articles % $limit) {
                       var_dump("last value recognition works");
                       goto offset_process;  // It is not a calling error, but the elements are over.
                   } else {

                       $this -> save_error( $save = true, $category_offset);
                      
                       echo "Marked error and moved to save_error";

                       break ;
                   }
               }
               $article = $obj["results"][$i];
               $item_id = array_values($article);

               //var_dump($i + 1 + ($j * $limit) - $limit + $plus_token);
               //var_dump($item_id[0]);

               $item_ids_array[$i] = $item_id[0];
              
               $i++;
           }

           $step_offset = $j * $limit + $plus_token;
           if ($step_offset>10000) {
               echo "more than 10000 elements jump";
               goto end_time; // Go to the time count, which is the end of the function
           } //Api impossible search greater than 10k

           $category_offset = $category . "&offset=" . $step_offset . "&limit=" . $limit . $access_token;
           var_dump($category_offset);

           /* Expansion test to exceed 10000 item limit */

           if($options_work['get_expansion'] == true){
           
             $start_time = microtime(true);

             $obj =  $this->get_articles_expansion($category_offset, $expansion_coefficient = 5);

             $end_time = microtime(true);
                static $quantity_expansion_cycle_articles = 0; 
             echo "save_expansion_count : " . $this->save_expansion_count($show_count = true) . "<br>";

             $quantity_expansion_cycle_articles = $this->save_expansion_count($show_count = true) - $quantity_expansion_cycle_articles;
             var_dump("quantity_expansion_cycle_articles : " . $quantity_expansion_cycle_articles);
             echo "<br> expansive method speed : " . round($quantity_expansion_cycle_articles/($end_time - $start_time), 4) . " art/seconds";
             $quantity_expansion_cycle_articles = $this->save_expansion_count($show_count = true);
           }

           /* Method without expansion "default"*/

           else{ $obj = $this->get_articles($category_offset);}

           // End of the expansion

           offset_process: // goto jump

           $this -> work_and_save($category, $item_ids_array, self::$SAVE_ARTICLES, $options_work_and_save);

           usleep(50000); // rest time for api so as not to overload it
       } 

        echo "Token pass is: " . $converted_res = ($pass_for_token) ? 'true' : 'false';

        if ($pass_for_token == true) {
            $plus_token = 1000;
            $total_articles = $save_token_items - $plus_token;
            $access_token = $save_access_token;
            //$limit=10;  //The method works fine at limit = 50

            $category_offset = $category . "&offset=" . $plus_token . "&limit=" . $limit . $access_token;
            
            $obj = $this->get_articles($category_offset);
                     
            $pass_for_token = false;

            goto token_process;
        } else {

          end_time: // goto when it exceeds 10k.
           $end_time = microtime(true);
            echo "<br> Rounded runtime: get_articles_total ->" . round($end_time - $start_time, 4) . " seconds";
           echo "<br>download speed : " . round(($i + 1 + ($j * $limit) - $limit + $plus_token + $this->save_expansion_count($show_count = true))/($end_time - $start_time), 4) . " art/seconds";
        }
    }

    public function get_articles_total_v2($category_id, $total_articles, $limit = 50, $country_id, $plus = null, $force_calculation_rand_under_1000){

      $start_time = microtime(true);

      $obj = $this -> get_country_articles($category_id . $plus, $country_id);

      if($total_articles == null){

        //var_dump("get_country_articles");
        //var_dump($obj);
        $total_articles = $obj["paging"]["total"];
      }else{

        if ($total_articles > $obj["paging"]["total"]) {
          $total_articles = $obj["paging"]["total"];
        } 
        //else continue with parameter input $total_articles        
      }

      var_dump("total articles");
      var_dump($total_articles);

      if ($total_articles <= 1000) {        

       //just_1000: // old code - when $force_calculation_under_1000 = true
       for ($j=1;  $j <= ceil($total_articles / $limit); $j++)
          {
            if ($j == ceil($total_articles / $limit)) {
              $last_limit = $total_articles % $limit;

              $category_array[] = $category_id . "&limit=" . $last_limit . "&offset=" . ($j-1)*50 . $plus;
            }
            else{
              $category_array[] = $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50 . $plus; 
            }              
          }

        //var_dump("categories");
        //var_dump($category_array);

        $total_items_category = $this -> get_real_articles_array_country($category_array, $country_id);

        var_dump("total_items_category");
        var_dump(count($total_items_category));
        //var_dump($total_items_category[0]);    
       
      }
      else{

        $item_groups = $this -> get_ranking_item_groups($total_articles, $category_id,  $offset_group = 1000, $country_id, $plus);

        $token_negative_offset = false;
        foreach ($item_groups["quantity_array"] as $key => $value) {

          //work with with big categories more than 1000 items in a filter price (499.9-500)
            if($item_groups["quantity_array"][$key] > 1000){
              $token_negative_offset = true;
              continue;
            }
                  
            for ($j=1;  $j <= ceil($item_groups["quantity_array"][$key] / $limit); $j++)
            {
              if ($j == ceil($item_groups["quantity_array"][$key] / $limit)) {
                $last_limit = $item_groups["quantity_array"][$key] % $limit;

                $category_array[] = $category_id . "&limit=" . $last_limit . "&offset=" . ($j-1)*50 . "&price=" . $item_groups['min_max_price'][$key]["min"] . "-" . $item_groups['min_max_price'][$key]["max"] . $plus;
              }
              else{
                $category_array[] = $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50 . "&price=" . $item_groups['min_max_price'][$key]["min"] . "-" . $item_groups['min_max_price'][$key]["max"] . $plus; 
              }              
            }

            var_dump("category_array");
            var_dump($category_array);

            $total_items_category_groups = $this -> get_real_articles_array_country($category_array, $country_id);

            if($total_items_category != null){
              foreach ($total_items_category_groups as $key => $value) {
                array_push($total_items_category, $total_items_category_groups[$key]);
              }
            }
            else{
              $total_items_category = $total_items_category_groups;
            }

            var_dump(count($total_items_category));
            //var_dump($total_items_category);

            unset($category_array);
        }

        var_dump("total items found");
        var_dump($total_items_category);

        if($force_calculation_rand_under_1000 == true){

          $items_to_rand = $total_items_category;
          unset($total_items_category);

          foreach ($items_to_rand as $key => $value) {
            $rand_value = mt_rand(0, count($items_to_rand) - 1);
            $total_items_category_unsort[$key] = $items_to_rand[$rand_value];
            $test_rand[$key] = $rand_value;

            if (count($total_items_category_unsort) == 1000) {
              $total_items_category_unsort = array_unique($total_items_category_unsort);
              $total_items_category_to_sort = $total_items_category_unsort;
              $new_key = 0;
              foreach ($total_items_category_to_sort as $key => $value) {
                $total_items_category[$new_key] = $total_items_category_to_sort[$key];
                $new_key = $new_key + 1;
              }
              break; // return a sort array of rand keys ex: (1, 3, 1001, 2007) where all keys are differents
            }
          }

          var_dump("rand keys selected");
          var_dump($test_rand);

          //var_dump("final rand output before array_unique");
          //var_dump($total_items_category);

          //$total_items_category = array_unique($total_items_category);

          var_dump("final rand output after array_unique");
          var_dump($total_items_category);
        }


      }

      $end_time = microtime(true);
        echo "<br> Rounded runtime: get_articles_total_v2 ->" . round($end_time - $start_time, 4) . " seconds";
        echo "<br>download speed : " . round(count($total_items_category)/($end_time - $start_time), 4) . " art/seconds";

      return $total_items_category;           
    }

    //get_ranking_item

    public function get_ranking_item_array($object_array, $limit = 50, $country_id){

      //Pendiente problema con soluciones de ranking que son null en vez de '' (string vacio)      
      //Pick categories.

      foreach ($object_array as $key => $value) {
          $categorys_existing[$key] = $object_array[$key]["category_id"];      
      }

      
      var_dump("categorys_existing before array_unique");
      var_dump($categorys_existing);

      $categorys_existing = array_unique($categorys_existing); //Remove duplicate values ​​from an array

      //fix key because array_unique can return for example (0 => string 'MLM4605', 7 => string 'MLM194084')
      $fix_key = 0;
      foreach ($categorys_existing as $key => $value) {
        $step[$fix_key] = $categorys_existing[$key];
        $fix_key = $fix_key + 1;
      }
      unset( $categorys_existing);
      $categorys_existing = $step;

      var_dump("categorys_existing  after array_unique");
      var_dump($categorys_existing);

      // form $item_id_array for every category existing

      foreach ($object_array as $item => $value_item) {
        foreach ($categorys_existing as $key => $value_category){
          if ($object_array[$item]["category_id"] == $categorys_existing[$key]) {
            $array_category_items[$key][$item] = $object_array[$item]["id"];
          }
        }
      }

      // send all item_id_array to get_ranking_item
      $ranking_array = [];
      foreach ($categorys_existing as $key => $value_category) {
        var_dump("array_category_items entrada");
        var_dump($array_category_items[$key]);
        var_dump("categorys_existing entrada");
        var_dump($categorys_existing[$key]);
        $obj = $this -> get_ranking_item($array_category_items[$key], $categorys_existing[$key], $limit = 50, $country_id);
        //$ranking_array = array_merge($ranking_array, $obj);
        $ranking_array = $ranking_array + $obj;
        var_dump("ranking_array");
        var_dump($ranking_array);
      }

      ksort($ranking_array);
      var_dump("ksort");
      var_dump($ranking_array); 

      return $ranking_array;
    }
 
    public function get_ranking_item($item_id_array, $category_id, $limit = 50, $country_id)
    {
        $start_time = microtime(true);

        //search total_articles in input category 

        $obj = $this->get_country_articles($category_id, $country_id);
       
        $total_articles = $obj["paging"]["total"];

        if ($total_articles <= 1000) {
          
          $ranking_position = $this -> get_ranking_item_less1000($item_id_array, $category_id, $total_articles, $limit = 50, $country_id);
          return $ranking_position;
        }
        else {

          //first go through the first 1000
          $ranking_position_less1000 = $this -> get_ranking_item_less1000($item_id_array, $category_id, 1000, $limit = 50, $country_id, $option_big_1000 = true);

          $ranking_position = $ranking_position_less1000["know"];

          //Pendiente crear if para $ranking_position_less1000["know"] vacio que me quite los warning cuando no hay elementos por debajo de 1000 en el ranking
          //var_dump("items less than 1000 inside a big category (bigger 1000)");
          //var_dump($ranking_position);

          //subtract item_array and item_array < or = 1000
          $item_array_to_search_bigger1000 = [];
          foreach ($item_id_array as $key_undone => $value) {
            foreach ($ranking_position as $key_done => $value) {
              if($key_undone == $key_done) // 
              {
                continue 2;               
              }
            }

            $item_array_to_search_bigger1000[$key_undone] = $item_id_array[$key_undone];       
          }

          var_dump("test subtract item arrays");
          var_dump($item_array_to_search_bigger1000);

          $item_groups = $this -> get_ranking_item_groups($total_articles, $category_id,  $offset_group = 1000, $country_id);

          var_dump("get_ranking_item_groups");
          var_dump($item_groups);
          
          $total_known_offset = 0;
          $token_negative_offset = false;
          foreach ($item_groups["quantity_array"] as $key => $value) {

            //work with with big categories more than 1000 items in a filter price (499.9-500)
            if($item_groups["quantity_array"][$key] > 1000){
              $token_negative_offset = true;
              continue;
            }
                  
            for ($j=1;  $j <= ceil($item_groups["quantity_array"][$key] / $limit); $j++)
            {
              if ($j == ceil($item_groups["quantity_array"][$key] / $limit)) {
                $last_limit = $item_groups["quantity_array"][$key] % $limit;

                $category_array[] = $category_id . "&limit=" . $last_limit . "&offset=" . ($j-1)*50 . "&price=" . $item_groups['min_max_price'][$key]["min"] . "-" . $item_groups['min_max_price'][$key]["max"];
              }
              else{
                $category_array[] = $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50 . "&price=" . $item_groups['min_max_price'][$key]["min"] . "-" . $item_groups['min_max_price'][$key]["max"]; 
              }              
            }

            //var_dump("categories");
            //var_dump($category_array);

            $total_items_category = $this -> get_real_articles_array_country($category_array, $country_id);

            //var_dump("total_items_category");
            //var_dump(count($total_items_category));
            //var_dump($total_items_category);

            //var_dump("all ranking_position_less1000");
            //ar_dump(count($ranking_position_less1000["all"]));
            //var_dump($ranking_position_less1000["all"]);           

            $item_know_ranking = array_intersect($total_items_category, $ranking_position_less1000["all"]);

            //var_dump("item_know_ranking");
            //var_dump("count");
            //var_dump(count($item_know_ranking));
            //var_dump("end in:");
            //end($item_know_ranking);
            //var_dump(key( $item_know_ranking ));
            //var_dump($item_know_ranking);

            unset($category_array);

          //get known offset

            $item_know_ranking = array_intersect($total_items_category, $ranking_position_less1000["all"]);
            $known_offset[$key] = count($item_know_ranking);

            $total_known_offset = $total_known_offset + $known_offset[$key];

          //get ranking_position on groups

            foreach ($total_items_category as $group_item_position => $obj) {
              

              //convert to array to use array_intersect as a validation
              $item_to_array = array(
                "0" => $total_items_category[$group_item_position]
              );

              if (($item_match = array_intersect($item_array_to_search_bigger1000, $item_to_array)) != null) {
                
                  // sum 1 because arrays start at 0, but ranking start on 1 
                  $groups_ranking_position[$key][key($item_match)] = $group_item_position + 1;                         
              }                            
            }               
          }

          var_dump("before known_offset");
          var_dump($known_offset);
          var_dump("known_offset all sum before complete_known_offset");
          var_dump($total_known_offset);
          var_dump("count ranking_position_less1000 all");
          var_dump(count($ranking_position_less1000["all"]));

          if ($token_negative_offset == true) {
             $known_offset = $this -> complete_known_offset($known_offset, $item_groups["quantity_array"], $total_known_offset, count($ranking_position_less1000["all"]));
          } 

          $total_known_offset_after_complete_known_offset = 0;
          foreach ($known_offset as $key => $value) {
            $total_known_offset_after_complete_known_offset = $total_known_offset_after_complete_known_offset + $known_offset[$key];
          }        
         
          var_dump("known_offset all sum after complete_known_offset");
          var_dump($total_known_offset_after_complete_known_offset);
          var_dump("after complete known_offset");
          var_dump($known_offset);
          var_dump("groups_ranking_position");
          var_dump($groups_ranking_position);

          $ranking_position_higher1000 = $this -> get_ranking_item_higher1000($known_offset, $item_groups["quantity_array"], $groups_ranking_position);
          
          var_dump("ranking_position_less1000");
          var_dump($ranking_position_less1000["know"]);
          var_dump('ranking_position_higher1000');
          var_dump($ranking_position_higher1000);

          if($ranking_position_less1000["know"] != null){
             if ($ranking_position_higher1000 == null) { 
               $ranking_position = $ranking_position_less1000["know"];}
             else{
               $ranking_position = $ranking_position_less1000["know"] + $ranking_position_higher1000;}
             
          }
          else{
            $ranking_position = $ranking_position_higher1000;
          }
          
          var_dump('ranking_position');
          var_dump($ranking_position);
        }
            
        // null result equal to empty string to machine learning understanding 
        foreach ($item_id_array as $key => $value) {
          if ($ranking_position[$key] == null) {
           $ranking_position[$key] = "";
         }
        }     

       $end_time = microtime(true);
       echo "<br> Rounded runtime: get_ranking_item ->" . round($end_time - $start_time, 4) . " seconds";

       return $ranking_position;
    }

    public function get_ranking_item_higher1000($known_offset_array , $quantity_array, $groups_ranking_position){

      //Pendiente hacer esta funcion comprobarla, arreglar todo el codigo y testear general el ranking con varios ejemplos. Es un gran paso!.

      foreach ($groups_ranking_position as $group_key => $value) {
        foreach ($groups_ranking_position[$group_key] as $item_position => $group_position) {

          // cycle for all groups assuming that the ranking increases proportionally in each group.

          $speed = $group_position/$known_offset_array[$group_key]; //adimensional
          //var_dump('speed');
          //var_dump($speed);
          $other_group_position = 0;
          $ranking_position[$item_position] = 0;
          foreach ($known_offset_array as $key => $value) {
            $other_group_position = $speed * $known_offset_array[$key];
            // if the estimated position in the group exceeds the size of the group, the position will be limited to the group size 
            if($other_group_position > $quantity_array[$key]){
              $other_group_position = $quantity_array[$key];
            }

            //var_dump('other_group_position');
            //var_dump($other_group_position);
            //var_dump('quantity_array');
            //var_dump($quantity_array[$key]);
            $ranking_position[$item_position] = $ranking_position[$item_position] + $other_group_position;
          }

          //convert float to integer
          $ranking_position[$item_position] = intval(round($ranking_position[$item_position], 0));
          
          //var_dump('ranking_position');
          //var_dump($ranking_position[$item_position]);
        }
      }

      var_dump('Final ranking_position');
      var_dump($ranking_position);

      return $ranking_position;
    }


    public function get_ranking_item_less1000($item_id_array, $category_id, $total_articles, $limit = 50, $country_id, $option_big_1000 = false){

        //Pendiente esta funcion se le tiene que modificar la busqueda por items , problema devuelve array no mayor a 50 size

         var_dump(" get_ranking_item_less1000 item_id_array input");
         var_dump($item_id_array);

         var_dump("get_ranking_item_less1000 total_articles");
         var_dump($total_articles);
          
        for ($j=1;  $j <= ceil($total_articles / $limit); $j++)
        {

          if($j>21){
            break;
            /*
            open door to access token used
            $category_array[] = $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50 . "&access_token=" . analysis::access_token();
            */
          }
          else{
          $category_array[] = $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50;
          }  
        }
        
        //var_dump('category_array');
        //var_dump($category_array);

        $total_items_category = $this -> get_real_articles_array_country($category_array, $country_id);

        if ($option_big_1000 == true){
          
          $ranking_position["all"] = $total_items_category;
          
          var_dump("get_ranking_item_less1000 ranking_position[all]");
          var_dump(count($ranking_position["all"]));
          //var_dump($ranking_position["all"]);         
        }
        else{
          $ranking_position = [];
        }

        // new method

        //get ranking_position on 1000 items search

        foreach ($total_items_category as $item_position => $obj) {
          
          //var_dump('test cicle');
          //var_dump($item_position);
          //convert to array to use array_intersect as a validation
          $item_to_array = array(
            "0" => $total_items_category[$item_position]
          );

          if (($item_match = array_intersect($item_id_array, $item_to_array)) != null) {

            if ($option_big_1000 == true){
              // this are elements (less than 1000) dont need more calculation.
              //var_dump('item_position');
              //var_dump($item_position);
              $ranking_position["know"][key($item_match)] = $item_position + 1;
            }
            else{
              //less than 1000
              
              $ranking_position[key($item_match)] = $item_position + 1;
            }                           
          }
          else{
            //testing purpuse
            $lost_item[$item_position + 1] = $obj;
          }
          if (count($ranking_position) == count($item_id_array)) {
            var_dump("check if the search is all complete, to save memory");
            break;
          }                            
        }

        var_dump("lost items");
        var_dump($lost_item);

        //end!
        /* 
        foreach ($total_items_category as $number_packages => $obj) {
          foreach ($total_items_category[$number_packages]["results"] as $key => $value) {

            //convert to array to use array_intersect as a validation
            $item_to_array = array(
              "0" => $total_items_category[$number_packages]["results"][$key]["id"]
            );

            if (($item_match = array_intersect($item_id_array, $item_to_array)) != null) {

              if ($option_big_1000 == true){
                // this are elements (less than 1000) how dont need more calculation.
                $ranking_position["know"][key($item_match)] = $total_items_category[$number_packages]["paging"]["offset"] + $key + 1;
              }
              else{
                // less than 1000
                $ranking_position[key($item_match)] = $total_items_category[$number_packages]["paging"]["offset"] + $key + 1;
              }         
            }            
          }

          // after 50 items, check if the search is all complete, to save memory

          if (count($ranking_position) == count($item_id_array)) {
            var_dump("check if the search is all complete, to save memory");
            break;
          }  
        }
        */

        if ($option_big_1000 == true){
          var_dump("item_less1000 that were found");
          var_dump(count($ranking_position["know"]));
          var_dump($ranking_position["know"]);
        }      

        return $ranking_position;    
    }

    public function complete_known_offset($known_offset, $item_groups_quantity_array, $total_known_offset, $count_ranking_position_less1000_all){

      //return known_offset without holds 
      //pediente valorar si $total_known_offset hace falta, 18 de enero parece innecesario de acuerdo al metodo usado ya que se basa en proporcion con resepcto al total de articulos 
      /*
      $total_articles = 0;
      foreach ($item_groups_quantity_array as $key => $value) {
        $total_articles = $total_articles + $item_groups_quantity_array[$key]; 
      }
      */

      //Calculate total_articles_in_holds
      $total_articles_in_holds = 0;
      foreach ($item_groups_quantity_array as $key_base => $value) {
        foreach ($known_offset as $key_compare => $value) {
          if ($key_base == $key_compare) {
          continue 2;
          }
        }

        $total_articles_in_holds = $total_articles_in_holds + $item_groups_quantity_array[$key_base];
      }

      //fills all offset holes

      $total_known_offset_holds = $count_ranking_position_less1000_all - $total_known_offset;

      foreach ($item_groups_quantity_array as $key_base => $value) {
        foreach ($known_offset as $key_compare => $value) {
          if ($key_base == $key_compare) {
          continue 2;
          }
        }

        $known_offset[$key_base] = (int)($total_known_offset_holds * $item_groups_quantity_array[$key_base]/$total_articles_in_holds);

      }

      ksort($known_offset); //organize array, could be deleated

      return $known_offset;
    }

    public function get_ranking_item_groups($total_articles, $category_id, $offset_group = 1000, $country_id, $plus = null)
    {

      //pendiente poner un limite de tiempo max para que la funcion no trabaje indefinidamente. puede caer en bucles infinitos.

      $start_time = microtime(true);

      var_dump("init get_ranking_item_groups");

      $min_max_price = $this -> groups_maker(0, $total_articles, $total_articles, $add_max_infinite = true); //assume (max_init = $total_articles);

      foreach ($min_max_price as $key => $value) {
           
        $category_array[] = $category_id . "&limit=1" . "&offset=0" . "&price=" . $min_max_price[$key]["min"] . "-" . $min_max_price[$key]["max"] . $plus; 
      }

      var_dump($category_array);   

      $total_items_category = $this -> get_articles_array_country($category_array, $country_id);

      foreach ($total_items_category as $key => $value) {
        $quantity_array[$key] = $total_items_category[$key]["paging"]["total"];
      }

      var_dump($quantity_array);

      // unified and separate until all quantity_array elements are < $offset_group (Commonly 1000)
       $count_cycle = 0;
       for ($key = 0;  $key < count($quantity_array); $key++) {

        repeat_unified_and_separate: // goto (start again after every "unified and separate")
        //var_dump("start separate and unified");
        //var_dump($key);
        
        if ($quantity_array[$key] > 1000) {
          $groups_maker_unified = $this -> groups_maker_unified($quantity_array, $min_max_price, $groups_max_size = 1000);

          var_dump($groups_maker_unified);

          $groups_maker_separate = $this -> groups_maker_separate($groups_maker_unified["quantity_array"]
          , $groups_maker_unified["min_max_price"], $groups_max_size = 1000, $category_id, $country_id, $plus);

          //Pendiente crear salida de esta funcion cuando resta en pesos (333 y 330.1) diferencia de 0.1

          var_dump($groups_maker_separate);

          $quantity_array = $groups_maker_separate["quantity_array"];
          $min_max_price =  $groups_maker_separate["min_max_price"];

          // repeat again the cycle but just 10 times for bugs. 
          // Pendiente valorar si 10 veces es mucho o poco testear esta funcion 1 hora 
          $key = 0;
          $count_cycle = $count_cycle + 1; 

          var_dump("count_cycle no more than 10");
          var_dump($count_cycle);

          if ($count_cycle < 10){
            goto repeat_unified_and_separate;
          }
          else{
            // More than 10 rounds on unify and separate, the model go out.
            break;
          }         
        }
        
        //var_dump("finish unified and separate");
        //var_dump($key);
      }

      $groups_maker_unified = $this -> groups_maker_unified($quantity_array, $min_max_price, $groups_max_size = 1000);

      $quantity_array = $groups_maker_unified["quantity_array"];
      $min_max_price = $groups_maker_unified["min_max_price"];

      var_dump("Final group formation");
      var_dump($quantity_array);
      var_dump($min_max_price);

      //Revisar suma "No match!" that s strange
      $sum = 0;
      foreach ($quantity_array as $key => $value) {
        $sum = $sum + $quantity_array[$key];
      }

      var_dump("sum all elements");
      var_dump($sum);

      $item_groups =  $groups_maker_unified;

      $end_time = microtime(true);
         echo "<br> Rounded runtime: get_ranking_item_groups ->" . round($end_time - $start_time, 4) . " seconds";

      return $item_groups;     
    }

    public function groups_maker($min_money, $max_money, $total_items, $add_max_infinite = false){

      $total_partision = ceil($total_items / 1000); 
      $range_to_be_partitioned = ($max_money - $min_money)/$total_partision;

      for ($i=0; $i < $total_partision; $i++){
        //var_dump("step groups_maker");
        //var_dump($i);
        $partision_range[$i]["min"] = round($min_money + $i * $range_to_be_partitioned, 1);
        $partision_range[$i]["max"] = round($min_money + ($i + 1)* $range_to_be_partitioned, 1);

        //Filter if there is more than 1000 in the same price
        //Pendiente the probability of being locked in the range to infinity is 0?.
        if (($partision_range[$i]["max"] - $partision_range[$i]["min"]) < 0.2) {

          var_dump("activate locked more than 1000 in one price");
          unset($partision_range);
          $partision_range[0]["min"] = $min_money;
          $partision_range[0]["max"] = $max_money;

          return $partision_range;
        }
      }

      //convert last max to infinite
      if ($add_max_infinite == true) {
        $partision_range[count($partision_range ) - 1]["max"] = "*";
      }

      return $partision_range;
    }

    public function groups_maker_separate($quantity_array_work, $min_max_price_work, $groups_max_size = 1000, $category_id, $country_id, $plus = null)
    {
    
      foreach ($quantity_array_work as $key => $value) {
        
        if ($quantity_array_work[$key] > $groups_max_size) {

          //rare case last element in quantity_array_work is > 1000 (adjust * "infinite") this need to be tested // 14 de enero tested it work correctly
          if ($min_max_price_work[$key]["max"] == "*") {
            $min_max_price_work[$key]["max"] = $min_max_price_work[$key]["min"] * 2;


            $min_max_price_separate = $this -> groups_maker($min_max_price_work[$key]["min"], $min_max_price_work[$key]["max"], $quantity_array_work[$key], $add_max_infinite = true);

            var_dump("min_max_price_separate add_max_infinite = true");
            var_dump($min_max_price_separate);
          }
          else{
            //common case
            $min_max_price_separate = $this -> groups_maker($min_max_price_work[$key]["min"], $min_max_price_work[$key]["max"], $quantity_array_work[$key], $add_max_infinite = false);
          }          
        }
        else{
          $min_max_price_separate = [];
          $min_max_price_separate[0] = $min_max_price_work[$key];
          //var_dump("min_max_price dont need to be separated");
          //var_dump($min_max_price_separate);
        }

        //cleaned all arrays
        if ($key == 0) {
          $min_max_price_cleaned = $min_max_price_separate;
          //var_dump("first after min_max_price_cleaned");
          //var_dump($min_max_price_cleaned);  
        }
        else{
          $min_max_price_cleaned = array_merge($min_max_price_cleaned, $min_max_price_separate);
          //var_dump("after min_max_price_cleaned");
          //var_dump($min_max_price_cleaned);  
        }                        
      }

      // Already calculated $min_max_price_cleaned now calculate $quantity_array_cleaned

      foreach ($min_max_price_cleaned as $key => $value) {
           //$offset_price = round($total_articles/($j*2), 1);
        $category_array[] = $category_id . "&limit=1" . "&offset=0" . "&price=" . $min_max_price_cleaned[$key]["min"] . "-" . $min_max_price_cleaned[$key]["max"] . $plus; 
      }

      var_dump($category_array);   

      $total_items_category = $this -> get_articles_array_country($category_array, $country_id);

      //var_dump($total_items_category);

      foreach ($total_items_category as $key => $value) {
        $quantity_array_cleaned[$key] = $total_items_category[$key]["paging"]["total"];
      }

      //var_dump($quantity_array_cleaned);

      $groups_maker_separate["quantity_array"] = $quantity_array_cleaned;
      $groups_maker_separate["min_max_price"] = $min_max_price_cleaned;

      return $groups_maker_separate;
    }

    public function groups_maker_unified($quantity_array_work, $min_max_price_work, $groups_max_size = 1000){

      $size_quantity_array = count($quantity_array_work);
      
      //unified
      for ($i = $size_quantity_array - 1; $i > 0; $i--){
        
        if (($quantity_array_work[$i] + $quantity_array_work[$i - 1]) <= $groups_max_size){

          $quantity_array_work[$i-1] = ($quantity_array_work[$i] + $quantity_array_work[$i - 1]); //unify 2 elements
          $quantity_array_work[$i] = null; //eliminate 1 element 
                          
          $min_max_price_work[$i-1]["min"] = $min_max_price_work[$i-1]["min"]; //same minimum
          $min_max_price_work[$i-1]["max"] = $min_max_price_work[$i]["max"]; //maximum unified

          $min_max_price_work[$i]["min"] = null;
          $min_max_price_work[$i]["max"] = null;
          
        }
        
      } 

      //clean null spaces
      $quantity_array_cleaned = [];
      $min_max_price_cleaned = []; 

      foreach ($quantity_array_work as $key => $value) {
        if ($quantity_array_work[$key] != null) {
          $quantity_array_cleaned[] = $quantity_array_work[$key];
          $min_max_price_cleaned[] = $min_max_price_work[$key];
        }
      }

      $groups_maker_unified["quantity_array"] = $quantity_array_cleaned;
      $groups_maker_unified["min_max_price"] = $min_max_price_cleaned;

      return $groups_maker_unified;
    }

    public function get_children_local($main_parent_category, $options_work_and_save, $category_offset = 0, $max_article_counter = 12000000)
    {
        //$category_offset to start calculating to divide the runs, $max_article_counter is adjusted to 12 million since the category that has the most article in MLM has 12 million items

        //Pendiente poner en funcion de $k_limite y count R) Creo que no es necesario por el moemtno aunque sí podría servir para categorias padres con muchas hojas se puede valorar sí es mejor que category_offset y max_article_counter para las corridas.

        $start_time = microtime(true);
        $k = 0;
        $item_counter = 0;
        //take time, hit counter, item counter

        $local = file_get_contents(__DIR__ . "/../categoriesMLM.json");

        $obj = json_decode($local, $assoc = true);
        
        var_dump(count($obj));
        var_dump($obj[$main_parent_category]['path_from_root'][0]["id"]);
                
        $obj_size = count($obj);
        $obj_num = array_values($obj);

        for ($i=0; $i < $obj_size; $i++) {
            if ($obj_num[$i]['path_from_root'][0]["id"] == $main_parent_category) {
                 
                 //Pendiente poner $obj_num[$i]['path_from_root'][2]["id"] identificar automaticamente que sea 2. R)  testear par valorar
                //Salvar la informacion de los articulos de pieza especifica de carro y enviarlo a mi gmail.

                if ($this->get_children($obj_num[$i]["id"]) == null) {
                    $k = $k + 1;
                    var_dump($i . " is " . $obj_num[$i]["id"]  . " - category sheet");

                    $obj = $this->get_articles($obj_num[$i]["id"]);
       
                    $total_articles = $obj["paging"]["total"];

                    $item_counter = $item_counter + $total_articles;

                    var_dump("item_counter : " . $item_counter);

                    if ($item_counter < $max_article_counter) {
                        if ($k < $category_offset) {
                            var_dump("skipped the number k : " . $k);
                            continue;
                        } else {
                            //Remember to update access_token before running
                            //Remember to update the php.ini for time limit

                            echo "k value to process " . $k;
                            $result_total = $this -> get_articles_total($obj_num[$i]["id"], 50, $options_work_and_save);
                            $this -> save_error($save = false);// $save=false It is equivalent to working errors
                        }
                    } else {
                        echo "The value k for which he stayed processing was " . $k;
                        $end_time = microtime(true);

                        
                        echo "<br>Get_children_local time rounded to stop: " . round($end_time - $start_time, 4) . " seconds";
                        return $k;
                    }
                } else {
                    //  var_dump($i . " es " . $obj_num[$i]["id"]  . " -  middle_child");
                }
            } 
            else {
                //var_dump($i . "negative");
            }
        }

        var_dump("Total hits are: " . $k);
        
        $end_time = microtime(true);

        echo "<br> Get_children_local time rounded: " . round($end_time - $start_time, 4) . " seconds";
    }

    public function get_major_father_categories($country_id){

      $url = "https://api.mercadolibre.com/sites/" . $country_id . "/categories";

      //This method looks for the json of the url and turns it into an object, $ assoc = true passes it from object to associative array.
     
      $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));
     
      $obj = json_decode($json, $assoc = true);

      $count_children = count($obj);
      
      for ($i=0; $i < $count_children; $i++) {
          $children_categories[$i] = $obj[$i]["id"];
      }
      
      return $children_categories;
    }

    public function get_cousin_categories_array($category_array, $country_base){

      $category_object = $this -> get_category_object($category_array);

      //get names of categories //

        foreach ($category_object as $key => $value) {
          $item_title_array[$key] = $category_object[$key]["name"];
          $test_category[] = $category_array[$key];
        }
        // MLB need to be traduce to portuguese
        $item_title_pt_array = analysis::traduce_to_pt_v2($item_title_array);
        var_dump("testing portuguesse");
        var_dump($item_title_pt_array);

      //get all cousin categories id //

        foreach (analysis::$Name_coin as $country_name => $coin) {

          var_dump("category items Country search : " . $country_name);

          // MLB need to be traduce to portuguese
          if ($country_name == "MLB") {
            $items_competition_search = analysis::custom_search_array_v2($item_title_pt_array, $plus = "&limit=1", $country_name);

            $predicted_category_pt = analysis::get_category_predictor_v2($item_title_pt_array, $country_name);

            foreach ($items_competition_search as $key => $value) {
            
              $category_id_cousins[$category_array[$key]][$country_name] = $predicted_category_pt [$key]["id"];
            }
          }
          elseif ($country_name == $country_base){
          //$country_base category is know so we jump it 

            foreach ($items_competition_search as $key => $value) {
              
              $category_id_cousins[$category_array[$key]][$country_name] = $category_array[$key];
            }
            continue; //next country
          }
          else{
             $items_competition_search = analysis::custom_search_array_v2($item_title_array, $plus = "&limit=1", $country_name);

             foreach ($items_competition_search as $key => $value) {
              
              $category_id_cousins[$category_array[$key]][$country_name] = $items_competition_search[$key]["results"][0]["category_id"];

              $test_category[] = $items_competition_search[$key]["results"][0]["category_id"];
            }
          }           
        }

        var_dump("category_id_cousins");
        var_dump($category_id_cousins);

      //testing category name //

        var_dump("categories_id to test");
        var_dump($test_category);
        $category_object_test = $this -> get_category_object($test_category);

        foreach ($category_object_test as $key => $value) {
          $item_title_array_test[$test_category[$key]] = $category_object_test[$key]["name"];
        }

        var_dump("categories_names to test");
        var_dump($item_title_array_test);

        foreach (analysis::$Name_coin as $country_name => $coin) {

          $items_competition_search = analysis::custom_search_array_v2($item_title_array, $plus = "&limit=1", $country_name);

          //$country_base category is know so we jump it 

          if ($country_name == $country_base) {    

            foreach ($items_competition_search as $key => $value) {
              $category_reference_name = $item_title_array_test[$category_array[$key]];
              $category_name_cousins[$category_reference_name][$country_name] = $item_title_array_test[$category_array[$key]];
            }
            continue; //next country
          }

          // MLB need to be traduce to portuguese

          if ($country_name == "MLB"){
            //$category_reference_name_pt = $item_title_array_test[$category_array[$key]];

            foreach ($items_competition_search as $key => $value) {
              
              $category_reference_name = $item_title_array_test[$category_array[$key]];
              $category_reference_name_pt = $item_title_pt_array[$key];
              var_dump("category_reference_name portuguese");
              var_dump($category_reference_name_pt);

              var_dump("category_reference_name portuguese to compare");
              //var_dump($item_title_array_test[$items_competition_search[$key]["results"][0]["category_id"]]);
              //$item_title_array_test[$test_category[$key + 2*count($category_array)]] = "Cafés da manhã e lanches";
              //var_dump($item_title_array_test[$test_category[$key + 2*count($category_array)]]); 
              //var_dump($test_category[$key + 2*count($category_array)]);  
              var_dump($predicted_category_pt [$key]["name"]);
              var_dump($predicted_category_pt [$key]["id"]);

              $equal_true_or_false = analysis::at_least_one_word_comparation($category_reference_name_pt, $predicted_category_pt[$key]["name"]);

              var_dump("at_least_one_word_comparation");
              var_dump($equal_true_or_false);

              // MLB position on array    
              /*
              if($category_reference_name_pt == $item_title_array_test[$test_category[$key + 2*count($category_array)]]){

                $category_name_cousins[$category_reference_name][$country_name] = $item_title_array_test[$test_category[$key + 2*count($category_array)]];
              }
              */
              if($equal_true_or_false){
                $category_name_cousins[$category_reference_name][$country_name] = $predicted_category_pt [$key]["name"];
              }
              else{
                $category_name_cousins[$category_reference_name][$country_name] = "dont_match";
                $category_id_cousins[$category_array[$key]][$country_name] = "dont_match";
              } 
            }
            continue; //next country
          }
         
          foreach ($items_competition_search as $key => $value) {

            $category_reference_name = $item_title_array_test[$category_array[$key]];

            if($category_reference_name == $item_title_array_test[$items_competition_search[$key]["results"][0]["category_id"]]){

              $category_name_cousins[$category_reference_name][$country_name] = $item_title_array_test[$items_competition_search[$key]["results"][0]["category_id"]];
            }
            else{
              $category_name_cousins[$category_reference_name][$country_name] = "dont_match";
              $category_id_cousins[$category_array[$key]][$country_name] = "dont_match";
            }         
          } 
        }

      var_dump("name testing output");
      var_dump($category_name_cousins);

      return $category_id_cousins;
    }

    public function get_all_fathers_of_children_categories_mexico_local(){

      $local = file_get_contents(__DIR__ . "/../categoriesMLM.json");

      $obj = json_decode($local, $assoc=true);
      $obj_num = array_values($obj);

      var_dump(count($obj));
      $obj_size = count($obj);

      //get children categories
      $step = 0;
      for ($i=0; $i < $obj_size; $i++) {

        if (!isset($obj_num[$i]["children_categories"][0]["id"])){
          $children_category[$step] = $obj_num[$i]["id"];
          $step = $step + 1;
        }
      }

      //look if want of the children categories are the younger categories
      $step = 0;
      for ($i=0; $i < $obj_size; $i++) {

        if (!isset($obj_num[$i]["children_categories"][0]["id"])) {
          continue;
        }

        foreach ($children_category as $key => $value) {
          
          if (($children_category[$key] == $obj_num[$i]["children_categories"][0]["id"])) {

            $father_of_children_category[$step] = $obj_num[$i]["id"];
            $step = $step + 1;
            break;
          } 
        }
      }
        
      return $father_of_children_category;
    }

    public function get_all_children_categories_mexico_local(){

      $local = file_get_contents(__DIR__ . "/../categoriesMLM.json");

      $obj = json_decode($local, $assoc=true);
      $obj_num = array_values($obj);
      
      var_dump(count($obj));
      $obj_size = count($obj);

      $step = 0;
      for ($i=0; $i < $obj_size; $i++) {

        if (!isset($obj_num[$i]["children_categories"][0]["id"])){
          $children_category[$step] = $obj_num[$i]["id"];
          $step = $step + 1;
        }
      }
        
      return $children_category;
    }

    public function custom_generalmatch_search($search, $plus, $return_object = false){

      //Pendiente arreglar match_ids como respuesta a match, ya que se devuelven objetos y strings "ids"

        foreach (analysis::$Name_coin as $country_name => $coin) {

            var_dump("Country search : " . $country_name);

            $country_search = analysis::custom_search($search, $plus, $country = $country_name, $pass_access_token = false);

            foreach ($country_search['results'] as $key => $value) {

                $titles_array_to_compare[$key] = $country_search['results'][$key]['title'];
            }

            $position_match_array = $this -> compare_title_preg_match_title_input($search, $titles_array_to_compare, $option_match = array("return_position_match" => true) , $remove_words_relatedto = null);

            $match_number = 0;

            foreach ($position_match_array as $key => $value) {

                if ($value) {

                    $match_item = $country_search['results'][$key];
                    //$match_object[$country_name][$match_number] = $country_search['results'][$key];
                    $match_ids[$country_name][$match_number] = $country_search['results'][$key]['id'];

                    $match_number = $match_number + 1;

                }   
            }
        }

        if ($return_object == true) {
          $count = 0;

          //group item to search answer
          foreach ($match_ids as $country_name => $value) {
            
            foreach ($match_ids[$country_name] as $item_id => $value) {
               
              $array_to_search[$count] = $match_ids[$country_name][$item_id];
              $count = $count + 1;
              //$save_position[$country_name][$item_id] = $item_id; // could be also null
            }
          }

          $object_vector = $this -> get_full_article_array($array_to_search);

          //reposition of object into return form
          $count = 0;
          foreach ($match_ids as $country_name => $value) {
            
            foreach ($match_ids[$country_name] as $item_id => $value) {
               
            $match_ids[$country_name][$item_id] = $object_vector[$count];
            $count = $count + 1;
              
            }
          }
        }

        return $match_ids;
    }

    // Data extractor solds_per_day //

    public function comparison_model_prepare_to_getfeatures_v2($min_children_category, $max_children_category, $plus, $force_calculation_under_1000, $country_base){

      $start_time = microtime(true);

      // obtein children categories match in Latinoamerica to pick data
      /*
        if ($country_base = "MLM") {
          $children_categories = $this -> get_all_children_categories_mexico_local();
        }
        else{
          return "just $country_base for MLM mexico";
        }

        var_dump($children_categories);

        $step = 0;
        for ($j = $min_children_category;  $j <= $max_children_category; $j++){

          $category_array[$step] = $children_categories[$j];
          $step = $step + 1;
        }

        var_dump($category_array);
      */
      // obtein father categories match in Latinoamerica to pick data

        if ($country_base = "MLM") {
          $fathers_of_children_categories = $this -> get_all_fathers_of_children_categories_mexico_local();
        }
        else{
          return "just $country_base for MLM mexico";
        }

        var_dump($fathers_of_children_categories);

        $step = 0;
        for ($j = $min_children_category;  $j <= $max_children_category; $j++){

          $father_category_array[$step] = $fathers_of_children_categories[$j];
          $step = $step + 1;
        }

        var_dump("father_category_array");
        var_dump($father_category_array);

        $father_children_array = $this -> get_children_array($father_category_array);
        $offset = 0;
        foreach ($father_children_array as $father_category => $value) {
          
          foreach ($father_children_array[$father_category] as $key => $value) {
            $category_array[$offset] = $father_children_array[$father_category][$key];
            $offset = $offset + 1;
          }
        }

        var_dump("children_categories input");
        var_dump($category_array);

        $category_cousins = $this -> get_cousin_categories_array($category_array, $country_base);

        $cousin_and_total_items["category_cousins"] = $category_cousins;

        var_dump($category_cousins);

      // obtein items in cousins categories  

        foreach ($category_cousins as $country_base_category => $value) {
          
          $total_items_Latinoamerica = 0;

          foreach ($category_cousins[$country_base_category] as $country_id => $value) {

            var_dump("country_base_category: " . $country_base_category . " country: " . $country_id);

            //eliminate dont match categories
            if ($category_cousins[$country_base_category][$country_id] == "dont_match") {
              $result_category_items[$country_id] = null;
              continue; //next country 
            }else{
              $match_category = $category_cousins[$country_base_category][$country_id];
            } 
            
            if($country_id == $country_base){

              $Tiny_data = false; // you want to prioritize that finds more data from the base country
              $array_to_search = $this -> get_articles_total_v2($match_category, $total_articles = null, $limit = 50, $country_id, $plus, $Tiny_data);

            }
            else{
              $array_to_search = $this -> get_articles_total_v2($match_category, $total_articles = null, $limit = 50, $country_id, $plus, $force_calculation_under_1000);

            }

            $total_items_Latinoamerica = $total_items_Latinoamerica + count($array_to_search);

            $result_category_items[$country_id] = $this -> get_full_article_array($array_to_search);     
          }

          //save items local

          var_dump("total_items_Latinoamerica");
          var_dump($total_items_Latinoamerica);
          $cousin_and_total_items["total_items"] = $total_items_Latinoamerica;

          $save_category = $country_base_category;
          $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";

          $dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . '_predictor.json';

          var_dump("dir_save");
          var_dump($dir_save);

          $this -> save_json($result_category_items, $dir_save);
          unset($result_category_items);
        }

      $end_time = microtime(true);

      echo "<br> indirect call - comparison_model_prepare_to_getfeatures- time rounded: " . round($end_time - $start_time, 6) . " seconds";

      return $cousin_and_total_items;
      
    }

    public function get_items_features_unified_v2($min_children_category, $max_children_category, $category_cousins, $country_base){

      $start_time = microtime(true);

      // get_items_features

        foreach ($category_cousins as $country_base_category => $value) {
          
          $local_file = $country_base_category . "_predictor";
          $result_general_category_items = $this -> load_json("predictorMachineLearning/" . $local_file . ".json");

          foreach ($result_general_category_items as $country_id => $value) {
            
            if ($result_general_category_items[$country_id] == null) {
              continue;
            }
            else{

              $items_features_category_items_country = $this -> get_items_features($result_general_category_items[$country_id], $features_to_obtain = null, $country_id);
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

          $this -> csv_to_machinelearning_v2($items_features_category_items, $country_base_category);
          unset($items_features_category_items);
        }


      $end_time = microtime(true);

      echo "<br> indirect call - get_items_features_unified- time rounded: " . round($end_time - $start_time, 6) . " seconds";
    }

    // Data extractor_2 solds_per_day (version 2)  //


    public function comparison_model_prepare_to_getfeatures_v3($min_children_category, $max_children_category, $plus, $country_base){

      //Diference vs v_2 
      //1- dont count with data from other countries, just $country_base

      $start_time = microtime(true);

      if ($country_base = "MLM") {
        $fathers_of_children_categories = $this -> get_all_fathers_of_children_categories_mexico_local();
      }
      else{
        return "just $country_base for MLM mexico";
      }

      var_dump($fathers_of_children_categories);

      $step = 0;
      for ($j = $min_children_category;  $j <= $max_children_category; $j++){

        $father_category_array[$step] = $fathers_of_children_categories[$j];
        $step = $step + 1;
      }

      var_dump("father_category_array");
      var_dump($father_category_array);

      $father_children_array = $this -> get_children_array($father_category_array);
      $offset = 0;
      foreach ($father_children_array as $father_category => $value) {
        
        foreach ($father_children_array[$father_category] as $key => $value) {
          $category_array[$offset] = $father_children_array[$father_category][$key];
          $offset = $offset + 1;
        }
      }

      var_dump("children_categories input");
      var_dump($category_array);

      // No cousins just country base and local category
      foreach ($category_array as $key => $value) {

        $category_id_cousins[$category_array[$key]][$country_base] = $category_array[$key];

        $categories_and_total_items["category_without_cousins"] = $category_id_cousins;
      }

      foreach ($category_array as $key => $value) {

        $Tiny_data = false; // you want to prioritize that finds more data from the base country
        $category_id = $category_array[$key];
        $array_to_search = $this -> get_articles_total_v2($category_id, $total_articles = null, $limit = 50,  $country_base, $plus, $Tiny_data);

        $total_items = $total_items + count($array_to_search);

        $result_category_items[$country_base] = $this -> get_full_article_array($array_to_search);

        //save items local

        var_dump("total_items");
        var_dump($total_items);
        $categories_and_total_items["total_items"] = $total_items;

        $save_category = $category_array[$key];
        $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";

        $dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning_v2/" . $save_category . '_predictor.json';

        var_dump("dir_save");
        var_dump($dir_save);

        $this -> save_json($result_category_items, $dir_save);
        unset($result_category_items);
      }

    $end_time = microtime(true);

    echo "<br> indirect call - comparison_model_prepare_to_getfeatures- time rounded: " . round($end_time - $start_time, 6) . " seconds";

    return $categories_and_total_items;

    }

    public function get_items_features_unified_v3($min_children_category, $max_children_category, $category_array, $country_base){

      //Diference vs v_2 
      //1- dont receive data from other countries, just $country_base
      //2- get_items_features_v2 lets you choose features
      //3- dont send to same direction on logic app

      $start_time = microtime(true);

      // get_items_features

        foreach ($category_array as $country_base_category => $value) {
          
          $local_file = $country_base_category . "_predictor";
          $result_general_category_items = $this -> load_json("predictorMachineLearning_v2/" . $local_file . ".json");

          foreach ($result_general_category_items as $country_id => $value) {
            
            if ($result_general_category_items[$country_id] == null) {
              continue;
            }
            else{

              $features_to_obtain = array("title", "site_id", "price", "reputation_vendor", "vendor_sales_completed", "logistic_type", "free_shipping", "ranking", "conversion", "sold_quantity", "views", "condition", "catalog_product_id", "video", "accepts_mercadopago", "tags", "num_pictures", "attributes", "reviews_average", "reviews_total", "official_store", "deal_ids", "warranty", "listing_type_id", "item_days_active","sold_quantity_for_days");

              $items_features_category_items_country = $this -> get_items_features_v2($result_general_category_items[$country_id], $features_to_obtain, $country_id);
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

          $this -> csvtoAzureBlob($items_features_category_items, $country_base_category);
          unset($items_features_category_items);
        }

      $end_time = microtime(true);

      echo "<br> indirect call - get_items_features_unified- time rounded: " . round($end_time - $start_time, 6) . " seconds";

    }


    public function csv_to_machinelearning_v2($items, $category_id){

      $Azure_logicApps_TrainingMachineLearning = "https://prod-19.centralus.logic.azure.com:443/workflows/96e3d6f34edc43be849ae7304c8fedf7/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=9rcOZAumI5eJy7kJEIK7ylJPIUkxN9LNNRnJAnogYZU";

      //$Azure_csvtoAzureBlob = "https://mlconexionmeli.azurewebsites.net/api/csvtoAzureBlob?code=xMNGNalSwHfaSpdkyfD/5lcvqftl8wBAfl7PKtX5qrQbyUsYmSqasQ==";
      
      $body = array(
        "items" => $items,
        "category_id" => $category_id
      );
      
      $save_category = $category_id;

      $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
      $dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . 'features_predictor.json';

      var_dump("dir_save");
      var_dump($dir_save);

      $this -> save_json($body, $dir_save);
      
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
          CURLOPT_CONNECTTIMEOUT => 180, 
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_TIMEOUT => 180
      );

      $opts = array(
          CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
          CURLOPT_POST => true, 
          CURLOPT_POSTFIELDS => $body
      );

      $uri = $Azure_logicApps_TrainingMachineLearning;

      $ch = curl_init($uri);
      curl_setopt_array($ch, $CURL_OPTS);
      curl_setopt_array($ch, $opts);

      $return["body"] = json_decode(curl_exec($ch), true);
      $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

      curl_close($ch);

      var_dump($return);
    }

    public function csvtoAzureBlob($items, $category_id){

     //$Azure_logicApps_TrainingMachineLearning = "https://prod-19.centralus.logic.azure.com:443/workflows/96e3d6f34edc43be849ae7304c8fedf7/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=9rcOZAumI5eJy7kJEIK7ylJPIUkxN9LNNRnJAnogYZU";

    $Azure_csvtoAzureBlob = "https://mlconexionmeli.azurewebsites.net/api/csvtoAzureBlob?code=xMNGNalSwHfaSpdkyfD/5lcvqftl8wBAfl7PKtX5qrQbyUsYmSqasQ==";
      
      $body = array(
        "items" => $items,
        "category_id" => $category_id
      );
      
      $save_category = $category_id;

      $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
      $dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . 'features_predictor.json';

      var_dump("dir_save");
      var_dump($dir_save);

      $this -> save_json($body, $dir_save);
      
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
          CURLOPT_CONNECTTIMEOUT => 180, 
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_TIMEOUT => 180
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
    }

    public function comparison_model_prepare_to_getfeatures($title_type, $title_brand, $title_model, $title_plus, $plus, $force_calculation_under_1000, $country_base){

      //It could be post in another class out of meli because it get complex
      
      $start_time = microtime(true);

      $result_general_category_items = $this -> category_generalitems_search($title_type, $title_brand, $title_model, $title_plus, $plus, $force_calculation_under_1000, $country_base);

      $title_search = $title_type . " " . $title_brand . " " . $title_model . " " . $title_plus;
      $title_search = preg_replace('/\s\s+/', ' ', $title_search); //Remove the remaining blanks spaces
      $title_search = trim($title_search);

      $step = analysis::get_category_predictor($title_search, $country_base);
      $save_category = $step["id"];

      $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
      $dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . '_predictor.json';

      var_dump("dir_save");
      var_dump($dir_save);

      $this -> save_json($result_general_category_items, $dir_save);

      $end_time = microtime(true);

      echo "<br> indirect call - comparison_model_prepare_to_getfeatures- time rounded: " . round($end_time - $start_time, 6) . " seconds";

      return $save_category . "_predictor";
    }

    public function get_items_features_unified($local_file, $title_search, $country_base){

      $start_time = microtime(true);

      $result_general_category_items = $this -> load_json("predictorMachineLearning/" . $local_file . ".json");

      foreach ($result_general_category_items as $country_id => $value){
            
        if ($result_general_category_items[$country_id] == null) {
          continue;
        }
        else{

          $items_features_category_items_country = $this -> get_items_features($result_general_category_items[$country_id], $features_to_obtain = null, $country_id);

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

      /*
      $items_features_category_items_country = $meli -> get_items_features($result_general_category_items["MLM"], $features_to_obtain = null, $country_id = "MLM");

      $items_features_category_items = $items_features_category_items_country;
      */

      $items_features_competition = $items_features_category_items;

      //$Azure_csvtoAzureBlob = "https://mlconexionmeli.azurewebsites.net/api/csvtoAzureBlob?code=xMNGNalSwHfaSpdkyfD/5lcvqftl8wBAfl7PKtX5qrQbyUsYmSqasQ==";

      $Azure_logicApps_TrainingMachineLearning = "https://prod-19.centralus.logic.azure.com:443/workflows/96e3d6f34edc43be849ae7304c8fedf7/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=9rcOZAumI5eJy7kJEIK7ylJPIUkxN9LNNRnJAnogYZU";

      $step = analysis::get_category_predictor($title_search, $country_base);
      $match_category = $step["id"];

      
      $body = array(
        "items" => $items_features_competition,
        "category_id" => $match_category
      );
      
      $save_category = $step["id"];

      $DEFAULT_SAVE_DIR = "wamp64/www/cursoPHP/mercadolibre_comparador/data/";
      $dir_save = $DEFAULT_SAVE_DIR . "predictorMachineLearning/" . $save_category . 'features_predictor.json';

      var_dump("dir_save");
      var_dump($dir_save);

      $this -> save_json($body, $dir_save);
      
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
          CURLOPT_CONNECTTIMEOUT => 180, 
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_TIMEOUT => 180
      );

      $opts = array(
          CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
          CURLOPT_POST => true, 
          CURLOPT_POSTFIELDS => $body
      );

      $uri = $Azure_logicApps_TrainingMachineLearning;

      $ch = curl_init($uri);
      curl_setopt_array($ch, $CURL_OPTS);
      curl_setopt_array($ch, $opts);

      $return["body"] = json_decode(curl_exec($ch), true);
      $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

      curl_close($ch);

      var_dump($return);

      $end_time = microtime(true);

      echo "<br> indirect call - get_items_features_unified- time rounded: " . round($end_time - $start_time, 6) . " seconds";
    }

    public function category_generalitems_search($title_type, $title_brand, $title_model, $title_plus, $plus, $force_calculation_under_1000, $country_base) 
    {

      foreach (analysis::$Name_coin as $country_name => $coin) {

          var_dump("category items Country search : " . $country_name);

          if ($country_name == $country_base) {

            $step = $force_calculation_under_1000;
            $step = false; // you want to prioritize that finds more data from the base country
            $result_category_items[$country_name] = $this -> category_localitems_search($title_type, $title_brand, $title_model, $title_plus, $plus, $country_name, $step);
          }
          else {

            $result_category_items[$country_name] = $this -> category_localitems_search($title_type, $title_brand, $title_model, $title_plus, $plus, $country_name, $force_calculation_under_1000);
          }
                  
      }     

      return $result_category_items;

    }

    public function category_localitems_search($title_type, $title_brand, $title_model, $title_plus, $plus, $country_id, $force_calculation_under_1000){

      static $reference_category;

      //first country MLA better category tree
      if ($reference_category == null) {
        
        $title_search = $title_type . " " . $title_brand . " " . $title_model . " " . $title_plus;

        //$step = analysis::get_category_by_ranking_search($title_search, $country_id);
        $step = analysis::get_category_predictor($title_search, "MLA");
        //$reference_category = $step;
        $reference_category = $title_search;
      }

      
      if($country_id == "MLA"){
        
        $match_category = $step["id"];
        //$match_category = $step;

        //$match_category = analysis::get_category_by_ranking_search($title_search, $country_id);
        
        //$reference_category = $match_category;
      }
      elseif($country_id == "MLB"){

        var_dump("reference_category");
        var_dump($reference_category);

        $reference_category_portuguese = analysis::traduce_to_pt($reference_category);

        var_dump("reference_category portuguese");
        var_dump($reference_category_portuguese);

         //$title_search = "Lâmina de substituição para barbeador elétrico Oneblade";

         //$step = analysis::get_category_predictor($reference_category_portuguese, $country_id);
         //$match_category = $step["id"];

         $match_category = analysis::get_category_by_ranking_search($reference_category_portuguese, $country_id);

      }
      else{
        
        //$step = analysis::get_category_predictor($reference_category, $country_id);
        //$match_category = $step["id"];



        $match_category = analysis::get_category_by_ranking_search($reference_category, $country_id);
      }

      var_dump("match_category");
      var_dump($match_category);

      $array_to_search = $this -> get_articles_total_v2($match_category, $total_articles = null, $limit = 50, $country_id, $plus, $force_calculation_under_1000);

      $total_items_category = $this -> get_full_article_array($array_to_search);

      return $total_items_category;
    }

    public function custom_generalcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus = null, $return_object = true, $expansion_method){

       foreach (analysis::$Name_coin as $country_name => $coin) {

          var_dump("Competition Country search : " . $country_name);

         $result_competition[$country_name] = $this -> custom_localcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus, $country_name, $return_object, $expansion_method);          
      }

      return $result_competition;
    }

    public function custom_localcompetition_search($title_type, $title_brand, $title_model, $title_plus, $plus, $country_id, $return_object = false, $expansion_method){

      //Pendiente crear un custom_generalcompetition_search para todos los paises y no solamente un pais en especifico

      if($country_id == "MLB"){

         $title_type = analysis::traduce_to_pt($title_type);
         $title_search = $title_type . " " . $title_brand . " " . $title_model . " " . $title_plus;
      }
      else{
        $title_search = $title_type . " " . $title_brand . " " . $title_model . " " . $title_plus;
     
      }
      var_dump("title search");
      var_dump($title_search);
      

      //take $match_category search
      /*
      $country_search = analysis::custom_search($title_search, $plus, $country = $country_id, $pass_access_token = false);

        
          foreach ($country_search["available_filters"] as $key => $value) {
            if("category" == $country_search["available_filters"][$key]["id"])
            {
              $match_category = $country_search["available_filters"][$key]["values"][0]["id"];
              break;
            }
          }
      */

      //search competition type + brand

      $short_search = $title_type . " " . $title_brand;  

      //take $match_category search
      
      $step = analysis::get_category_predictor($short_search, $country_id);
      $match_category = $step["id"];

      if ($plus == null) {
        $plus_category = "&category=" . $match_category;
      }
      else {
        $plus_category = "&category=" . $match_category . $plus;
      }  

      $country_search = analysis::custom_search($short_search, $plus_category, $country = $country_id, $pass_access_token = false);


      /*
      if(($expansion_method == "total_items_competition") && ($country_search["paging"]["total"] == 0)){

        var_dump("test country_search null asnswer");
        var_dump($country_search);

        //repeat search if category_predictor filter, throws a response zero items, in order to obtain information.
        $short_search = $title_type;
        $country_search = analysis::custom_search($short_search, $plus_category, $country = $country_id, $pass_access_token = false);

        var_dump("after test country_search null asnswer");
        var_dump($country_search);
      }
      */

      //take total_items_competition
      
      $total_items_competition = $country_search["paging"]["total"];
      

      //take "primary_results"
   
      $primary_results_items_competition = $country_search["paging"]["primary_results"];
      

        if($expansion_method == "total_items_competition_match_category"){
        
          for ($j=1;  $j <= ceil($total_items_competition / $limit=50); $j++)
            {

            if($j>2){
              //Pendiente poner de limite 100 articulos para que revisar el machine learning.. puede expandirse a 1000 , puede expandirse a otros paises

              break;
              
              //$plus_array[] = "&category=" . $category_id . "&limit=" . $limit . "&offset=" . //($j-1)*50 . "&access_token=" . analysis::access_token();
              
            }
            else{

              if ($j == ceil($total_items_competition / $limit)) {
                $last_limit = $total_items_competition % $limit;

                $plus_array[] = "&category=" . $match_category . "&limit=" . $last_limit . "&offset=" . ($j-1)*50 . $plus_category;
              }
              else{
                $plus_array[] = "&category=" . $match_category . "&limit=" . $limit . "&offset=" . ($j-1)*50 . $plus_category;
              }         
            }  
          } 
        }
        elseif($expansion_method == "primary_results_items_competition"){

          for ($j=1;  $j <= ceil($primary_results_items_competition / $limit=50); $j++)
          {

            if($j>21){
              //Pendiente poner de limite 100 articulos para que revisar el machine learning.. puede expandirse a 1000 , puede expandirse a otros paises

              break;
              
              //$plus_array[] = "&category=" . $category_id . "&limit=" . $limit . "&offset=" . ($j-1)*50 . "&access_token=" . analysis::access_token();
              
            }
            else{

              if ($j == ceil($primary_results_items_competition / $limit)) {
                $last_limit = $primary_results_items_competition % $limit;

                $plus_array[] = "&category=" . $match_category . "&limit=" . $last_limit . "&offset=" . ($j-1)*50 . $plus_category;
              }
              else{
                $plus_array[] = "&category=" . $match_category . "&limit=" . $limit . "&offset=" . ($j-1)*50 . $plus_category;
              }
            }  
          }              
        }
        elseif ($expansion_method == "total_items_competition") {

          // search only by title, soft search
          $short_search = $title_type;

          $country_search = analysis::custom_search($short_search, $plus_category, $country = $country_id, $pass_access_token = false);

          $total_items_competition = $country_search["paging"]["total"];
          var_dump("total_items_competition");
          var_dump($total_items_competition);

          for ($j=1;  $j <= ceil($total_items_competition / $limit=50); $j++)
            {

            if($j>2){
              //Pendiente poner de limite 100 articulos para que revisar el machine learning.. puede expandirse a 1000 , puede expandirse a otros paises

              break;
              
              //$plus_array[] = "&category=" . $category_id . "&limit=" . $limit . "&offset=" . //($j-1)*50 . "&access_token=" . analysis::access_token();
              
            }
            else{

              if ($j == ceil($total_items_competition / $limit)) {
                $last_limit = $total_items_competition % $limit;

                $plus_array[] = "&category=" . $match_category . "&limit=" . $last_limit . "&offset=" . ($j-1)*50 . $plus_category;
              }
              else{
                $plus_array[] = "&category=" . $match_category . "&limit=" . $limit . "&offset=" . ($j-1)*50 . $plus_category;
              }         
            }  
          }
        }
        else {
          return "select a correct expansion_method";
        }
    
      //search items_competition ($match_category and $short_search)

      //var_dump("test country_search");
      //var_dump($country_search);

      var_dump("plus array custom_localcompetition_search");
      var_dump($plus_array);  

      var_dump("total_items_competition found");
      var_dump($total_items_competition);

      var_dump("primary_results_items_competition found");
      var_dump($primary_results_items_competition);

      $items_competition_search = analysis::custom_search_array($short_search, $plus_array, $country, $pass_access_token = false);

      //var_dump("0.5-Test items_competition");
      //var_dump(count($items_competition_search));
      //var_dump($items_competition_search);

      foreach ($items_competition_search as $search => $value) {
        foreach ($items_competition_search[$search]["results"] as $count_item => $item) {

          if ($return_object == true) {
            $items_competition[/*$count_item + $offset*/] = $items_competition_search[$search]["results"][$count_item];
          }
          else{
           //$return_object = false "return ids"  
           $items_competition[/*$count_item + $offset*/] = $items_competition_search[$search]["results"][$count_item]["id"];
          }                
          
        }    
      }

      var_dump("1-Test items_competition");
      var_dump(count($items_competition));

      //Transform (search object) to item object format, in order to unify with custom_generalmatch_search 

      foreach ($items_competition as $item_id => $value) {
               
        $array_to_search[$item_id] = $items_competition[$item_id]["id"];
      }

      var_dump("2-Test items_competition");
      var_dump($array_to_search);

      $items_competition = $this -> get_full_article_array($array_to_search);

      return $items_competition;     
    }
     
    public function file_force_contents($dir, $contents)
    {
        //Force the creation and replacement of the directory address
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "/$part")) {
                mkdir($dir);
            }
        }
        file_put_contents("$dir/$file", $contents);
    }

    public function work_and_save($category, $item_ids_array, $dir_save, $options_work_and_save = array())
    {
        //This function does not return argument is responsible for working and saving local information    
        // Pending Assess pass to switch // Pending appraise transform it into a filter to put all the necessary features

      //////////////    Work model     ////////////////////

      if (array_key_exists('get_inactive', $options_work_and_save) &&  $options_work_and_save['get_inactive'] == true) {
          echo "get_inactive process initiated ";
          $obj_array = analysis::get_full_article_inactive_array($item_ids_array);
      }

      elseif (array_key_exists('get_conversion', $options_work_and_save) &&  $options_work_and_save['get_conversion'] == true){

          echo "get_conversion process initiated ";
          $obj_array = analysis::get_full_article_conversion_array($item_ids_array, $options_work_and_save = array());
           /* Save and rewrite above a .json*/
           $only_category_number = $this -> explode_string($category, 'category');

           if (array_key_exists('more_views', $options_work_and_save) &&  $options_work_and_save['more_views'] == true){$action = "_views";} 
           elseif (array_key_exists('more_solds', $options_work_and_save) &&  $options_work_and_save['more_solds'] == true){$action = "_solds";}
           elseif (array_key_exists('more_conversion', $options_work_and_save) &&  $options_work_and_save['more_conversion'] == true){$action = "_conversion";}
           else {$action = null;}
      }

      else{
           //echo "Not especial options_work process initiated "; 
          $obj_array = $this->get_full_article_array($item_ids_array);
      }

      //////////////       Save Model      //////////////////// 

      if (array_key_exists('dir_to_save', $options_work_and_save)) {
        $dir_save = $options_work_and_save['dir_to_save'];
      }

      if (array_key_exists('get_conversion', $options_work_and_save) &&  $options_work_and_save['get_conversion'] == true) {

        // Save only in one file all the information
           var_dump("save it in one file");
           $dir = $dir_save . $only_category_number . "/" . "datos" . $action . ".json";
           $json = json_encode($obj_array);
           $save_it = $this->file_force_contents($dir, $json);
           goto already_save;
      }

      if (array_key_exists('dont_save', $options_work_and_save) &&  $options_work_and_save['dont_save'] == true){
        echo "<br> Data was not saved, only processed";
         already_save:
          return;
      }

      // saved model to download items 

       else {
        $only_category_number = $this -> explode_string($category, 'category');
        foreach ($obj_array as $key => $value) {  
            // Saved for each items in a json file       
            $dir = $dir_save . $only_category_number . "/" . $item_ids_array[$key] . ".json";
            $json = json_encode($obj_array[$key]);
            $save_it = $this->file_force_contents($dir, $json);
        }
      }
        
        //open_articles It is used to test by opening the file where it is saved by: $open_it = $this->open_articles($dir);
    }

    public function save_articles($category, $item_ids_array, $dir_save, $options_save = array()){

     $only_category_number = $this -> explode_string($category, 'category');
     
     if (array_key_exists('more_views', $options_save) &&  $options_save['more_views'] == true){$action = "_views";} 
     elseif (array_key_exists('more_solds', $options_save) &&  $options_save['more_solds'] == true){$action = "_solds";}
     elseif (array_key_exists('more_conversion', $options_save) &&  $options_save['more_conversion'] == true){$action = "_conversion";}
     else {$action = null;}

     $dir = $dir_save . $only_category_number . "/" . "datos" . $action . ".json";
     $json = json_encode($item_ids_array);
     $save_it = $this->file_force_contents($dir, $json);
      
    }

    public function save_json($json, $dir_save){

      $json = json_encode($json);

      try {
         $save_it = $this -> file_force_contents($dir_save, $json);
         var_dump("correct save: " . $dir_save);
       } catch (Exception $e) {
         var_dump($e);
       }  
    }

    public function load_json($dir_save){

      $local = file_get_contents("C:/wamp64/www/cursoPHP/mercadolibre_comparador/data/" . $dir_save);

      var_dump("C:/wamp64/www/cursoPHP/mercadolibre_comparador/data/" . $dir_save);
      $obj = json_decode($local, $assoc = true);

      return $obj;
    }

    public function save_error($save = true, $category_offset_error = 0){
    
      //// "if" save error mode, "else" work error mode
      static $k = 0;
      static $category_error;  
      
      if ($save == true){   

      $offset = $this -> explode_string($category_offset_error, 'offset'); 

          if ($offset > 10000) {
            return; //For values ​​greater than 10,000 this method cannot be used. The api blocks. //Pendiente borrar 
          }
          else{

          $k = $k + 1; 
          var_dump("Of all the marked errors, this is the error #: " . $k); 
          $category_error[$k] = $category_offset_error; 
          var_dump($category_error);


          //save local data_error
          //1- category_error
          //2- $GLOBALS["options_work_and_save"]
          /*
           $data_error[$k] = array(
            'category_error' => $category_error[$k], 
            'options_work_and_save' => $GLOBALS["options_work_and_save"]
          );
           
          $save_error_local = local::save_error_local($data_error[$k], $k);

          $save_it = $this->file_force_contents($save_error_local['dir'], $save_error_local['json']); 
          */

          }
      }
      else{

        //Por aquí me quedé 8/11/2019
              
        $this -> work_error($category_error);
        
      }
    }
    
    public function work_error($category_error){
     
      static $ok_jump_this_one;

      foreach ( $category_error as $key => $value) {
        //var_dump($key);
        if ($category_error[$key] === 0 || $ok_jump_this_one[$key] === true) {
          $category_error[$key] = 0;
          //$save_error_local = local::save_error_local($data_error = null, $key); //Pendiente revisar como reacciona ante el null
          //$erase_it = $this->file_force_contents($save_error_local['dir'], $save_error_local['json']); 

          //Pendiente poner el destructor (llamada a local para destruir el archivo)
          continue; 
        }  
        
        var_dump("Work Category error: " . $category_error[$key]);   
          $obj = $this->get_articles($category_error[$key]);
          $total_articles = $obj["paging"]["total"];
          var_dump($total_articles); //Pendiente valorar 
          $i = 0;
          $limit = $this -> explode_string($category_error[$key], 'limit');         
          $category = $this -> explode_string($category_error[$key], 'category');           
          $offset = $this -> explode_string($category_error[$key], 'offset');
          
            while ($i < $limit) {
               if ($obj["results"][$i] == null) {
                   if (($total_articles - $offset) < $limit && $i == $total_articles % $limit) {
                       var_dump("last value recognition works");
                       $ok_jump_this_one[$key] = true; //error mark already solved
                       goto offset_process;  // It is not a calling error, but the elements are over.
                   } else {
                       var_dump("Error #: " . $key . " was not resolved");
                       var_dump($category_error[$key]);
                       $ok_jump_this_one[$key] = false;
                       continue 2; //jump, so that the error is solved on another occasion that is called work_error ()
                   }

               }

               if ($i == $limit-1) {
                 
                 $ok_jump_this_one[$key] = true; // error mark already solved
                 
                 var_dump("yeah!! solved the error #:" . $key);
                 var_dump($category_error[$key]);
                 
               }

               $article = $obj["results"][$i];
               $item_id = array_values($article);

               //var_dump($i + 1 + ($j * $limit) - $limit + $plus_token);
               //var_dump($article);
               //var_dump($item_id[0]);

               $item_ids_array[$i] = $item_id[0]; 

               $i++;
           }
           
           offset_process:  // goto jump

           $this -> work_and_save($category, $item_ids_array, self::$SAVE_ARTICLES, $GLOBALS["options_work_and_save"]);
           var_dump($item_ids_array);
        }

        var_dump("after");
        var_dump($category_error);

        var_dump("jump this ones");
        var_dump($ok_jump_this_one);
    }

    public function open_articles($dir)
    {
        
        $local = file_get_contents("C:/" . $dir);
               
        $obj = json_decode($local, $assoc=true);
        
        var_dump($obj);
    }

    /********           expansive method with monte carlo          *********/

    public function get_articles_expansion($category, $expansion_coefficient)
    {
        //This first article json format is short (little info)
        
        $url = self::$GET_ARTICLES_URL . $category;

        $only_category_number = $this->explode_string($category, 'category'); //remove the "offset" and "limit" leave only the category

        $json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, $assoc = true);

        $size_array = count($obj['results']); if ($size_array < $expansion_coefficient) { return;}
        
        for ($i=0; $i < $expansion_coefficient; $i++) {
          $rand = random_int(0, $size_array-1);
          $item_title = $obj['results'][$rand]['title'];
          $this->expansion_search($item_title, $only_category_number, $expansion_coefficient);    
        }
              
        return $obj;
    }

    public function expansion_search($item_title, $only_category_number, $expansion_coefficient){

    echo "Item : " . $item_title . " has been expanded <br>"; 
      $search_items = analysis::custom_search($item_title, "&category=" . $only_category_number, $country = "MLM");

      $size_array = count($search_items['results']); if ($size_array < $expansion_coefficient * 2 ) { return;}

      if ($search_items["paging"]["total"] > 50) {

        $plus = "&offset=" . strval($search_items["paging"]["total"] - 50) . "&limit=50"; 
        analysis::custom_search($item_title, "&category=" . $only_category_number . $plus, $country = "MLM");  

        for($i=0; $i < 50; $i++) {
     
          $article = $search_items["results"][$i];
          $item_id = array_values($article);
          $result_array[$i] = $item_id[0];    
        }
      }
      else {
        return ;
      }
 
        var_dump($result_array);
        $result = $this->work_and_save($only_category_number, $result_array, self::$SAVE_ARTICLES, $GLOBALS["options_work_and_save"]);
        $this->save_expansion_count($show_count = null);

    }

    public function save_expansion_count($show_count = null){

      static $expansion_count = 0;
      
      if ($show_count == true) { return $expansion_count;}
      else{$expansion_count = $expansion_count + 50;}

    }

    /***   Final expansive method   ***/

    public static function explode_string($category, $option)
    {
      //Performance: "fastest" it takes to work a call average 4.0E-5 seconds

      $portions = explode("&", $category);

      switch ($option) {

        case 'category':
          return $portions[0]; // the search is set so that category goes first

        case 'nickname':
        foreach ($portions as $key => $value) {
          if (preg_match("/nickname/i", $portions[$key])){
            $order_and_value = preg_split('/=/', $portions[$key], -1, PREG_SPLIT_OFFSET_CAPTURE); //array of 2 arrays example: {$order_and_value[0][0]=limit and $order_and_value[1][0]= 50};
            $value = $order_and_value[1][0]; //
            return $value;           
          }         
        }  

        case 'offset':

          foreach ($portions as $key => $value) {
            if (preg_match("/offset/i", $portions[$key])){
              $order_and_value = preg_split('/=/', $portions[$key], -1, PREG_SPLIT_OFFSET_CAPTURE); 
              $value = $order_and_value[1][0]; 
              return $value;          
            }         
          } 

        case 'limit':

          foreach ($portions as $key => $value) {
            if (preg_match("/limit/i", $portions[$key])){
              $order_and_value = preg_split('/=/', $portions[$key], -1, PREG_SPLIT_OFFSET_CAPTURE); 
              $value = $order_and_value[1][0]; //
              return $value;           
            }         
          }
                       
        default:
         echo "Error in declaration of option";
         return;
      }
    }

    /********           Item Macheo          *********/

    /* options_method_to_compare = array(
       "compare_title" => "similar_text",
       "compare_title" => "preg_match",
       "compare_price" => "lineal"
       )

    */

    public function compare_items($item_to_match, $items_array_to_compare, $options_method_to_compare = null){
 
      //array_unshift add $ item_to_match, as the first element of the array to search 
      $array_to_search = $items_array_to_compare;
      array_unshift($array_to_search, $item_to_match); 

      //get json with features
      $obj_array = $this->get_full_article_array($array_to_search);

      if ($options_method_to_compare == null) {
        
      $result["title_match_percent"] = $this -> compare_title_similar_text($obj_array, $items_array_to_compare);
      $result["price_match_percent"] = $this -> compare_price_lineal($obj_array, $items_array_to_compare);
      return $result;

      }

      //Compare title "similar_text"

      if ((array_key_exists('compare_title',  $options_method_to_compare) && $options_method_to_compare['compare_title'] == "similar_text"))
      { 
        $result = $this -> compare_title_similar_text($obj_array, $items_array_to_compare);
        return $result; 
      } 

      //Compare titulo "preg_match" Check that all the words in A are in B
      
      if ((array_key_exists('compare_title',  $options_method_to_compare) && $options_method_to_compare['compare_title'] == "preg_match"))
      {
        if ((array_key_exists('remove_words',  $options_method_to_compare) && $options_method_to_compare['remove_words'] != null))
        {$result = $this -> compare_title_preg_match($obj_array, $items_array_to_compare, $options_method_to_compare['remove_words']);} 
        else{
        $result = $this -> compare_title_preg_match($obj_array, $items_array_to_compare, $remove_words_relatedto = null);
        } 
        return $result;                 
      }
      
      //Compare price "linear" price linear comparison model

       if ((array_key_exists('compare_price',  $options_method_to_compare) &&   $options_method_to_compare['compare_price'] == "lineal"))
      {
        $result = $this -> compare_price_lineal($obj_array, $items_array_to_compare);
        return $result;
      }
    }

    public function compare_title_similar_text($obj_array, $items_array_to_compare){

        $first = $this -> remove_words_and_lower($obj_array[0]['title'], $words_to_remove = "cell");
        var_dump($first);
        foreach ($items_array_to_compare as $key => $value) {
          $second = $this -> remove_words_and_lower($obj_array[$key + 1]['title'], $words_to_remove = "cell");
          var_dump($second);
          similar_text($first, $second, $percent);
          $compare_percent_titles[$key] = $percent;
        }

        return $compare_percent_titles; //answer is an array of percent

    }

    public function compare_price_lineal($obj_array, $items_array_to_compare){

      foreach ($items_array_to_compare as $key => $value) {

          //$percent = 100 - 100*abs($obj_array[0]['price'] - $obj_array[$key + 1]['price'])/$obj_array[0]['price'] it could be another method;

          $percent = 100/($obj_array[$key + 1]['price']/$obj_array[0]['price']);
          if ($percent > 100){$percent = 100/($obj_array[0]['price']/$obj_array[$key + 1]['price']);}
          $compare_percent_prices[$key] = $percent ;
          var_dump($obj_array[0]['price']);
          var_dump($obj_array[$key + 1]['price']);
        }

        return $compare_percent_prices; //answer is an array of percent
        
    }

    public function compare_title_preg_match($obj_array, $items_array_to_compare, $remove_words_relatedto = null){

      $options_save = array();

      $first = $this -> remove_words_and_lower($obj_array[0]['title'], $words_to_remove = "cell");
      
      $portions = explode(" ", $first);
      $m = 0;

      var_dump("It has a number of words equal to:");
      var_dump(count($portions));
      var_dump($portions);

      foreach ($items_array_to_compare as $i => $value) {
        //$i goes from 0 to 50
        $second = $this -> remove_words_and_lower($obj_array[$i + 1]['title'], $words_to_remove = "cell");
        $k = 0;
        //var_dump("word to compare");
        //var_dump($second);

        foreach ($portions as $j => $value) {
          
          preg_match("/" . $portions[$j] . "/", $second, $matches, PREG_OFFSET_CAPTURE);

          if ($matches[0] !== null) {
           
          $k = $k+1;
          
          } // compare every word there is in $second
          else {break;} //with a single mistake, it jumps to compare with another title

          if ($k == count($portions)) {

          var_dump("match was found!"); 
          $result[$m] = $obj_array[$i + 1];
          
          $m = $m + 1;

          }
        }
      }

      return $result; //answer: an array of size from 0 to 50 with the full body of the items that match the name of the item to compare
    }

    public function compare_title_preg_match_title_input($title_base, $titles_array_to_compare, $option_match = array(), $remove_words_relatedto = null)
    {

      $first = $this -> remove_words_and_lower($title_base, $words_to_remove = $remove_words_relatedto);

      $portions = explode(" ", $first);

      $m = 0;

      //var_dump("It has a number of words equal to:");
      //var_dump(count($portions));
      //var_dump($portions);

      foreach ($titles_array_to_compare as $i => $value) {
        // var_dump("It has a number of words equal to:");
        //var_dump(count($portions));

        //$i goes from 0 to 50

        //var_dump($i);

        $second = $this -> remove_words_and_lower($titles_array_to_compare[$i], $words_to_remove = $remove_words_relatedto);
        $k = 0;

        //var_dump("word to compare");
        //var_dump($second);

        foreach ($portions as $j => $value) {

          $matches[0] = null;
          
          preg_match("/" . $portions[$j] . "/i", $second, $matches, PREG_OFFSET_CAPTURE); // ¨i¨ 

          //var_dump($matches);

          if (empty($matches[0])) {
             $result_position[$i] = false;  
             break;
          }
          else {
            $k = $k+1;
          }

          /*
          if ($matches[0] != null) {
           //
          $k = $k+1;
          
          } // compare every word there is in $second
          else {
          $result_position[$i] = false;  
          break;
          } //with a single mistake, it jumps to compare with another title
          */

          if ($k == count($portions)) {

          var_dump("match was found!"); 
          $result[$m] = $titles_array_to_compare[$i];

          $result_position[$i] = true;
          
          $m = $m + 1;

          }
        }    
      }

      if ((array_key_exists('return_position_match',  $option_match) &&   $option_match['return_position_match'] == true))
        { 
         //1 de diciembre me quede por aca testear toda esta funciòn.

          return $result_position;

        }
        else{
          return $result; //answer: an array of size from 0 to 50 with titles of the items that match the name of the item to items_array_to_compare

        }
    }  

    public function remove_words_and_lower($subjet, $words_to_remove = array()){

      //Pendiente se puede adicionar una variable que traduzca

      if ($words_to_remove == "colores") {
        $words_to_remove = array('negro', 'azul', 'marron', 'marrón', 'gris', 'verde', 'naranja', 'rosa', 'purpura', 'púrpura', 'rojo', 'blanco', 'amarillo', 'carbón', 'carbon');
      }
      elseif ($words_to_remove == "cell") {
        $words_to_remove = array('color', 'negro', 'azul', 'marron', 'marrón', 'gris', 'verde', 'naranja', 'rosa', 'purpura', 'púrpura', 'rojo', 'blanco', 'amarillo', 'carbón', 'carbon', 'oscuro', 'océano', 'oceano', 'nuevo', 'caja sellada', 'desbloqueado', 'original', 'nacional', 'sellado', 'nacional sellado', '100% sellado','libre', 'liberado', 'super', 'super precio','regalo', '+4 regalos','+3 regalos','+2 regalos','+1 regalo','factura', '1 año', '2 años', '3 años', 'a', 'ante', 'bajo', 'cabe', 'con', 'contra', 'de', 'desde', 'durante', 'en', 'entre', 'hacia', 'hasta', 'mediante', 'para', 'por', 'según', 'sin', 'so', 'sobre', 'tras', 'versus', 'vía');
      }
      else{
        $words_to_remove = array(); //Pendiente need to be tested
      }

      $subjet = preg_replace('/\s\s+/', ' ', $subjet); //Remove the remaining blanks spaces

      $subjet = strtolower($subjet); //All in lowercase
      $words = explode(" ", $subjet); //convert from string to array

      foreach ($words as $i) {
        foreach ($words_to_remove as $j) {
          if ($i === $j) {
            $replace = "/" . $i . "/";
            $subjet = preg_replace($replace, ' ', $subjet); //returns the whole sentence lowercase 
          }
        }
      }

      $subjet = preg_replace('/\s\s+/', ' ', $subjet); //Remove the remaining blanks spaces

      return $subjet;
    }
    
}
