<?php

/**
 * melicurl_model
 */

class Curl
{
    public static function request_multiple($urls, $options = array())
    {
        $start_time = microtime(true);
        // Pendiente diseño para que reciva de 200 en 200 ordenes
        // 6-2-2020 it works better with $limit_calls = 100, it should be tested with internet connection
        $limit_calls = 10; 

        if (count($urls) > $limit_calls) {
            //var_dump("request_multiple more than " . $limit_calls);

            $total_calls = count($urls);

            $portions_urls = $urls;

            //var_dump("before portions_request_multiple count");
            //var_dump($total_calls);

            unset($urls);

            //$last_key = 0;
            for ($j=0;  $j <= floor($total_calls / $limit_calls); $j++)
            {
                var_dump("count");
                var_dump($j);

                foreach ($portions_urls as $key => $value) {
                    if ($j == floor($total_calls / $limit_calls) && ($key == $total_calls % $limit_calls)){
                        break;
                    }
                    if ($key >= $limit_calls) {
                        //$last_key = $key;
                        break;
                    }
                    $urls[$key] = $portions_urls[$key + $j * $limit_calls];
                } 

                $portions_request_multiple = curl::portions_request_multiple($urls, $options);
                unset($urls);

                if($result != null){
                    foreach ($portions_request_multiple as $key => $value) {
                        array_push($result, $portions_request_multiple[$key]);
                    }
                }
                else{
                    $result = $portions_request_multiple;
                }
            }
         
        }
        else{
            $result = curl::portions_request_multiple($urls, $options);
        }

        //var_dump("after portions_request_multiple count");
        //var_dump(count($result));

        $end_time = microtime(true);

        //echo "<br> request_multiple - portions -time rounded: " . round($end_time - $start_time, 6) . " seconds";

        return $result;
    }

    public static function portions_request_multiple($urls, $opciones = array())
    {
        //para ver diferentes $opciones ver https://www.php.net/manual/es/function.curl-setopt.php        

        $curly = array();
        $resultado = array();
        $pm = curl_multi_init();
        foreach ($urls as $id => $d) {
            $curly[$id] = curl_init();
            if (is_array($d) && !empty($d['url'])) {
                $url = $d['url'];
            } else {
                $url = $d;
            }
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
            if (!empty($d['post'])) {
                curl_setopt($curly[$id], CURLOPT_POST, 1);
                curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
            }
            if (!empty($opciones)) {
                curl_setopt_array($curly[$id], $opciones);
            }
            curl_multi_add_handle($pm, $curly[$id]);
        }
        $ejecutando = null;
        do {
            curl_multi_exec($pm, $ejecutando);
        } while ($ejecutando > 0);
        foreach ($curly as $id => $c) {
            $resultado[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($pm, $c);
        }
        curl_multi_close($pm);
        return $resultado;
    }

    public static function file_get_contents_curl($url, $opciones = array())
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, $opciones);
        
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    } 

    public static function post_request($body, $url){

      var_dump("antes de json_encode");
      var_dump($body);

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

      $uri = $url;

      $ch = curl_init($uri);
      curl_setopt_array($ch, $CURL_OPTS);
      curl_setopt_array($ch, $opts);

      $curl_response["body"] = json_decode(curl_exec($ch), true);
      $curl_response["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

      curl_close($ch);

      var_dump($curl_response);

      return $curl_response["body"];
    } 

    public static function post_portions_request_multiple($bodys, $urls, $opciones = array())
    {
        //para ver diferentes $opciones ver https://www.php.net/manual/es/function.curl-setopt.php       
    
        $curly = array();
        $resultado = array();
        $pm = curl_multi_init();
        foreach ($urls as $id => $d) {
            $curly[$id] = curl_init();
            $url = $urls[$id];
            
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curly[$id], CURLOPT_CONNECTTIMEOUT, 180);
            curl_setopt($curly[$id], CURLOPT_TIMEOUT, 180);

            $bodys[$id] = json_encode($bodys[$id]); //encode to send as a body

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

            var_dump($bodys[$id]);
            
            $opts = array(
              CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
              CURLOPT_POST => true, 
              CURLOPT_POSTFIELDS => $bodys[$id]
            );

            curl_setopt_array($curly[$id], $opts);

            if (!empty($opciones)) {
                curl_setopt_array($curly[$id], $opciones);
            }
            curl_multi_add_handle($pm, $curly[$id]);
        }
        $ejecutando = null;
        do {
            curl_multi_exec($pm, $ejecutando);
        } while ($ejecutando > 0);
        foreach ($curly as $id => $c) {
            $resultado[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($pm, $c);
        }
        curl_multi_close($pm);
        var_dump($resultado);
        return $resultado;
    }  
}
