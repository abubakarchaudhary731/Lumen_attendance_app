<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\Controller;
use FastRoute\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for the application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Public routes
$router->group(['prefix' => 'api/v1'], function () use ($router) {
    // Authentication Routes
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
    $router->post('refresh', 'AuthController@refresh');
});

// Protected routes
$router->group(['prefix' => 'api/v1', 'middleware' => 'auth:api'], function () use ($router) {
    // Auth routes that need authentication
    $router->post('logout', 'AuthController@logout');
    $router->get('me', 'AuthController@me');

    // User Routes
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->post('/', 'UserController@store');
        $router->get('/{id}', 'UserController@show');
        $router->put('/{id}', 'UserController@update');
        $router->delete('/{id}', 'UserController@destroy');
    });

    // Attendance Routes
    $router->group(['prefix' => 'attendance'], function () use ($router) {
        $router->get('/', 'AttendanceController@index');
        $router->post('check-in', 'AttendanceController@checkIn');
        $router->post('check-out', 'AttendanceController@checkOut');
        $router->get('/{id}', 'AttendanceController@show');
    });
});
