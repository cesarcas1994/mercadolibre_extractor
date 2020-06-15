<?php

/**
 * local_model
 */
class Local extends Meli
{
	
	public static function read_article_local($dir, $item_id = null)
	{
	//Remember! Add the item_id with some function find a name within a directory
			
		$local = file_get_contents("C:/wamp64/www/cursoPHP/mercadolibre_comparador/data/" . $dir . $item_id);
		$obj = json_decode($local, $assoc = true);
		var_dump($obj);

		return $obj;
	}

	public static function read_dir_local($dir)
	{
		$path = "C:/wamp64/www/cursoPHP/mercadolibre_comparador/data/" . $dir;
		$directory = opendir($path);

		while ($file = readdir($directory)) {
			if (is_dir($file)) {
				//var_dump("[" . $file  . "]" . "<br>");
				//echo $file;
			}
			else{
				echo $path . "/" . $file;
				var_dump(filetype($path . "/" . $file));
				echo $file  . "<br/>";
			}
		}
	}
	
	public static function work_errors_local($dir){

		local::read_dir_local("errors/");

		return ;
	}

	public static function destroy_local_json($time_dir){

		$dir_error = parent::$DEFAULT_SAVE_DIR . "errors/";

		if ($dir_error . 'error-' . $time_dir) {
			# code...
		}
		return ;
	}

	public static function save_error_local($data_error, $error_number){

	  static $firstcall = true;
	  
      if ($firstcall === true) {
      	$time = analysis::today($exact_time = true);
        $time_dir =   $time['hours'] . ":" . $time['minutes'] . ":" . $time['seconds'] .  "_" . $time['mday'] . "-" . $time['mon'] . "-" . $time['year']; 
	  	$firstcall = $time_dir;
	  } 

      $dir = parent::$DEFAULT_SAVE_DIR . "errors/" . 'error-' . $time_dir . "/" . $error_number . ".json";
      

      $json = json_encode($data_error);

      $save_error_local = array(
      	'dir' => $dir, 
      	'json' => $json,
      	'time_dir' => $time_dir
  	  );

      return $save_error_local;
	}
}