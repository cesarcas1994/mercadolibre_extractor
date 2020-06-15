<?php

/**
 * meli_controller.php
 */

/*
Database connection
ej:
require_once ("model/productsmodel.php");

$products=new productsmodel();

$result_products=$products->get_products();

*/

//Get token_automatic (heroku security issues)

//require_once ("model/callback_model.php");

require_once ("model/meli_model.php");

$meli = new Meli;

//require_once ("model/search_model.php");

//require_once ("model/10_biggest_seller_model.php");

//require_once ("model/data_extractor_model.php");

require_once ("model/data_extractor_model_2.php");

//require_once ("model/all_market_search_comparison_model.php");

//require_once ("model/cell_comparison_model.php");

//require_once ("model/ibushak_model.php"); 

//require_once ("model/trends_diary_model.php");

//require_once ("model/all_items_category_model.php");

//require_once ("model/day_weight_model.php");

//require_once ("model/machine_learning_out_meli_model.php");



