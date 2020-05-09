<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    // return $router->app->version();
    return 'Welcome to the '.env('APP_NAME').'!';
});

$router->post('product', 'ProductController@create');
$router->get('product', 'ProductController@read');
$router->put('product', 'ProductController@update');
$router->patch('product', 'ProductController@update');
$router->delete('product', 'ProductController@delete');

// Brand
$router->get('brand', 'BrandController@get');
// Category
$router->post('category', 'CategoryController@add');
$router->get('category', 'CategoryController@get');
$router->delete('category', 'CategoryController@remove');

// Image
$router->post('image', 'ProductController@addImage');
$router->get('image', 'ProductController@getImage');
$router->put('image', 'ProductController@setDefaultImage');
$router->patch('image', 'ProductController@setDefaultImage');
$router->delete('image', 'ProductController@removeImage');

// Movement (CRD)
$router->post('movement', 'MovementController@create');
$router->get('movement', 'MovementController@read');
// Movement hanya bisa dibaca, ditambah dan disoft-delete!
// $router->put('movement', 'MovementController@update');
// $router->patch('movement', 'MovementController@update');
$router->delete('movement', 'MovementController@delete');
$router->group(['prefix'=>'movement'], function() use($router) {
    // Movement Type
    $router->get('type', 'MovementController@readType');

    // Movement Detail
    $router->post('detail', 'MovementController@createDetail');
    $router->get('detail', 'MovementController@readDetail');
    $router->put('detail', 'MovementController@updateDetail');
    $router->patch('detail', 'MovementController@updateDetail');
    $router->delete('detail', 'MovementController@deleteDetail');

    // Movement Serial
    $router->post('serial', 'SerialController@create');
    $router->get('serial', 'SerialController@read');
    $router->put('serial', 'SerialController@update');
    $router->patch('serial', 'SerialController@update');
    $router->delete('serial', 'SerialController@delete');

    /**
     * Proses alokasi stok dilakukan menggunakan
     * database trigger, function dan stored procedure.
     * Sehingga backend hanya menerima data movement
     * kemudian database otomatis mengolah data yang dikirimkan tersebut.
     */
    // $router->post('allocation', 'AllocationController@create');
    // $router->get('allocation', 'AllocationController@read');
    // $router->put('allocation', 'AllocationController@update');
    // $router->patch('allocation', 'AllocationController@update');
    // $router->delete('allocation', 'AllocationController@delete');
});
