<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\Controller;
use FastRoute\Route;

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
    return $router->app->version();
});

// Public routes
$router->group(['prefix' => 'v1'], function () use ($router) {
    // Authentication Routes
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
    $router->post('refresh', 'AuthController@refresh');
});

// Protected routes
$router->group(['prefix' => 'v1', 'middleware' => 'auth'], function () use ($router) {
    // Auth routes that need authentication
    $router->post('logout', 'AuthController@logout');
    $router->get('me', 'AuthController@me');

    // User Routes
    $router->get('users', 'UserController@index');
    $router->post('users', 'UserController@store');
    $router->get('users/{id}', 'UserController@show');
    $router->put('users/{id}', 'UserController@update');
    $router->delete('users/{id}', 'UserController@destroy');

    // Attendance Routes
    $router->post('attendance/check-in', 'AttendanceController@checkIn');
    $router->post('attendance/check-out', 'AttendanceController@checkOut');
    $router->get('attendance', 'AttendanceController@index');
    $router->get('attendance/{id}', 'AttendanceController@show');
});
