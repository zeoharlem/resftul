<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Router;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Micro\Collection as MicroCollection;

$loader = new Loader();
$loader->registerDirs(array(
    __DIR__ . '/models/',
    __DIR__ . '/controllers/',
))->register();


$di     = new FactoryDefault();
$di->set('db', function(){
    return new Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host"      => "localhost",
        "username"  => "root",
        "password"  => "",
        "dbname"    => "peprice"
    ));
});

$eventsManager  = new EventsManager();
//Listen to all the application events
$eventsManager->attach('micro', function($event, $app){
    if($event->getType() == 'beforeExecuteRoute'){
        
    }
});

$app    = new Micro($di);
$router = new Router();
$collection = new MicroCollection();

$collection->setPrefix('/api/v1');
$collection->setHandler(new PostController());


/**
 * Using controllers for accessing
 */
$collection->get('/', 'index');

/**
 * Get all the products
 */
$app->get("/api/v1/products", function() use ($app){
    
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

$app->setEventsManager($eventsManager);
$app->mount($collection);
$app->handle();