<?php

/**
 * day_weight_model
 */

ini_set('memory_limit', '4095M'); // or you could use 4Giga

//  Pick min and max of the 1659 father categories in the case of mexico //

$min_father_category = 8;
$max_father_category = 9;

$start_time = microtime(true);

//$fathers_category_array = array('0' => 'MLM194325',
//'1' => 'MLM194324');

//$father_items = $meli -> get_items_children_of_father_category($fathers_category_array, $total_items = 5000, $country_base = "MLM", $force_less_than_1000 = true);
//var_dump("father_items");
//var_dump($father_items);

$meli -> get_and_storage_day_weight_v2($min_father_category, $max_father_category, $country_base = "MLM");
$meli -> process_day_weight_and_storage_v2($min_father_category, $max_father_category, $country_base = "MLM");

//$item_ids_array  = array(0 => "MLM680595257", 1 => "MLM751717818");

//$items_days_visits_array_1 = analysis::visits_item_ndays($item_ids_array, 365);
//var_dump("method 1");
//var_dump($items_days_visits_array_1);

//$items_days_visits_array_2 = analysis::visits_item_ndays_v2($item_ids_array);
//var_dump("method 2");
//var_dump($items_days_visits_array_2);

//$meli -> get_and_storage_day_weight_v2($min_children_category, $max_children_category, $country_base = "MLM");
//$meli -> get_and_storage_day_weight($min_children_category, $max_children_category, $country_base = "MLM");


//$fathers_of_children_categories = $meli -> get_all_fathers_of_children_categories_mexico_local();
//var_dump($fathers_of_children_categories);

/*$item_ids_array = array(
0 => "MLM724373594",
1 => "MLM714753216"
);*/
/*
$item_ids_array = array(
0 => "MLM560843104",
1 => "MLM549397284",
2 => "MLM724373594"
);

$item_id = "MLM560843104";
*/
//$items_days_visits_array = analysis::visits_item_ndays_v2($item_ids_array, 365);
//var_dump($items_days_visits_array);
/*
$items_days_visits_array = analysis::visits_item_ndays($item_ids_array, 365); 

//$items_days_visits_array = analysis::visits_item_ndays_v2($item_ids_array, 365);
var_dump($items_days_visits_array);

$items_52_week_visits_array = $meli -> get_week_visits($item_ids_array, $items_days_visits_array, 52);

var_dump($items_52_week_visits_array);

$week_difference_in_wrap = $items_52_week_visits_array[$item_id]['last_1week'] - 
$items_52_week_visits_array[$item_id]['last_52week'];

$week_difference_in_wrap_percent = $week_difference_in_wrap/$items_52_week_visits_array[$item_id]['last_52week'];

var_dump($week_difference_in_wrap_percent);
*/
//$meli -> get_and_storage_day_weight($min_children_category, $max_children_category, $country_base = "MLM");

// Plus things remenber delete

// Test curl multiple post
/*
$visits_item_json = '{
  "0":{
    "father_category_id":"MLM194325",
    "MLM194325" : {
        "MLM5998928733":{
            "2020-01-01":{
              "date":"2020-01-01",
              "visits":6  
            },
            "2020-01-02":{
              "date":"2020-01-02",
              "visits":10  
            },
            "2019-01-02":{
              "date":"2020-01-02",
              "visits":10  
            }
        },
        "MLM553785582":{
            "2020-01-01":{
              "date":"2020-01-01",
              "visits":14  
            }
        }
    }
},
  "1":{
      "father_category_id":"MLM4212",
      "MLM4212" : {
          "MLM5998928733":{
              "2020-01-01":{
                "date":"2020-01-01",
                "visits":12  
              },
              "2020-01-02":{
                "date":"2020-01-02",
                "visits":20  
              }              
          },
          "MLM553785582":{
              "2020-01-01":{
                "date":"2020-01-01",
                "visits":8  
              }
          }
      }
  }
  
}';

	var_dump($visits_item_json);

	$visits_item_array = json_decode($visits_item_json, true);

	var_dump("before post");
	var_dump($visits_item_array);
	

$urls = array('0' => "https://prod-16.centralus.logic.azure.com:443/workflows/9e7b998364864cb9bcb6d3dee0bcfcd1/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=8d4YXsc47jdiU-ewhGiDHu4Du6BMn2o5SC4NW1gCSBQ", 
'1' => "https://prod-16.centralus.logic.azure.com:443/workflows/9e7b998364864cb9bcb6d3dee0bcfcd1/triggers/manual/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=8d4YXsc47jdiU-ewhGiDHu4Du6BMn2o5SC4NW1gCSBQ");

$visits_weight_test =  $meli -> calculate_day_weight($visits_item_array);
var_dump("visits_weight_test");
var_dump($visits_weight_test);

//curl::post_portions_request_multiple($visits_item_array, $urls, $opciones = null);

//curl::post_request($visits_item_array[0], $urls[0]);
*/
$end_time = microtime(true);

echo "<br> indirect call - day_weight_model - time rounded: " . round($end_time - $start_time, 6) . " seconds";
//echo "<br>download speed : " . round($cousin_and_total_items["total_items"]/($end_time - $start_time), 4) . " art/seconds";