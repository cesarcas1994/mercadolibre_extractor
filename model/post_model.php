<?php

/**
 * post_model
 */
class Post
{
	protected static $POST_REVIEW_URL = "https://api.mercadolibre.com/items/validate?access_token=";
	protected static $API_ROOT_URL = "https://api.mercadolibre.com";

	public static $CURL_OPTS = array(
		//revisar si es necesario esta linea
        //CURLOPT_USERAGENT => "MELI-PHP-SDK-2.0.0", 
        // revisar si es necesario que este en true 
        //CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );

    public static $TEST_USER_21_10 = array(
		
        "id" => 482014423,
		"nickname" => "TEST0U15Z7CP",
		"password" => "qatest4152",
		"site_status" => "active",
		"email" => "test_user_69192887@testuser.com"
    );
	
	

	public static function create_item($title, $category_id, $price, $currency_id, $available_quantity, $buying_mode, $listing_type_id, $condition, $description, $video_id, $warranty, $pictures_url_array = array()){


		foreach ($pictures_url_array as $key => $value) {
			$pictures_url_array_format = array( "source" =>  $pictures_url_array[$key]);
		}

		$item = array(
			"title" => $title,
			"category_id" => $category_id,	
			"price" => $price,
			"currency_id" => $currency_id,
			"available_quantity" => $available_quantity,
			"buying_mode" => $buying_mode,
			"listing_type_id" => $listing_type_id,
			"condition" => $condition,
			"description" => $description,
			"video_id" => $video_id,
			"warranty" => $warranty,
			"pictures" => array($pictures_url_array_format)
            
		);

		// Busca el title en la data de MELI para matchear el formato, se toman 5 elementos para buscar uno que tenga el mismo category_id.

		// 1 via usando busqueda personalizada 
        $result = analysis::custom_search($title, "&limit=5", $country = "MLM", $pass_access_token = false);

		foreach ($result['results']	 as $key => $value) {
			if ($result['results'][$key]['title'] == $title) {
				$item_match_ej = $result['results'][$key];
				break;
			}
			else{
				return "No se encuentra matcheo para " . $title . " mediante metodo busqueda personalizada";
			}
		}

        return $item_match_ej;

		// 2 via usando busqueda por categoria 

        //var_dump($item_match_ej);

		//$item_match_ej = post::match_item_bycategory($category);

		//var_dump($result);

		//return $item;
	}
    /*
    public static function create_item_v2(){
        {
            "title": "Item de test - No Ofertar",
            "category_id": "MLA3530",
            "price": 10,
            "currency_id": "ARS",
            "available_quantity": 1,
            "buying_mode": "buy_it_now",
            "listing_type_id": "gold_special",
            "description": {
                "plain_text": "Descripción con Texto Plano  \n"
            },
             "video_id": "YOUTUBE_ID_HERE",
             "tags": [
                "immediate_payment"
            ], 
             "attributes": [{
                "id": "ITEM_CONDITION",
                "value_id": "2230582"
            }]
            "sale_terms": [{
                    "id": "WARRANTY_TYPE",
                    "value_id": "2230279"
                },
                {
                    "id": "WARRANTY_TIME",
                    "value_name": "90 dias"
                }

            ]
            "pictures": [{
                "source": "http://mla-s2-p.mlstatic.com/968521-MLA20805195516_072016-O.jpg"
            }]
        }
    }
    */
	//Pendiente Valorar crear vía para colocar imagenes en items subiendolas de tu servidor y mediante "put" ponerlas en tu item.

	//validador de items

	public static function validator_item($body = null){

		/* primera manera manual

		$url = self::$POST_REVIEW_URL . analysis::access_token();

		$json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

		$obj = json_decode($json, $assoc);

		return $obj;

		*/

		//segunda manera usando ya la función post

		 $response = post::post_item('/items/validate', $body, array('access_token' => analysis::access_token()));

		 return $response;

	}
	//recordar borrar $params

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

    public static function put_item($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_CUSTOMREQUEST => "PUT",
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

    public static function post_make_path($path, $params = array()) {
        if (!preg_match("/^\//", $path)) {
            $path = '/' . $path;
        }

        $uri = self::$API_ROOT_URL . $path;
        
        if(!empty($params)) {
            $paramsJoined = array();

            foreach($params as $param => $value) {
               $paramsJoined[] = "$param=$value";
            }
            $params = '?'.implode('&', $paramsJoined);
            $uri = $uri.$params;
        }

        return $uri;
    }

    //Funcion Crear usuario prueba
    public static function create_test_user($site_id){
    	
    	$body = array(
    		"site_id" => $site_id
    	);

    	$response = post::post_item('/users/test_user', $body, array('access_token' => analysis::access_token()));

		return $response;
    }

    public static function match_item_bycategory($category){

    	$url = "https://api.mercadolibre.com/sites/MLM/search?category=" . $category . "&limit=1";

    	$json = curl::file_get_contents_curl($url, $options = array(CURLOPT_SSL_VERIFYPEER => false));

        $obj = json_decode($json, true);

        return $obj["results"][0];

        //var_dump( $obj["results"][0]);

    }

    public static function closed_item_id($path, $params){

        $body = array(
            "status" => "closed"
        );

        post::put_item($path, $body, $params = array());
    }

    public static function delete_item_id($path, $params){

        $body = array(
            "deleted" => "true"
        );

        post::put_item($path, $body, $params = array());
    }


}