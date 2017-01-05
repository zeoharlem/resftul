<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Router;
use Phalcon\Di\FactoryDefault;

$app    = new Micro();
$router = new Router();
$collection = new Micro\Collection();


/**
 * Get all the products
 */
$app->get("/api/v1/products", function(){
    
});

//Search and get products by category id
$app->get("/api/v1/category/{category_id}", function($category_id){
    
});

//Search and get products by category id
$app->get("/api/v1/product/{category_id}", function($category_id){
    
});

//Search and get products by category id
$app->get("/api/v1/product/delete/{primary_id}", function($category_id){
    
});