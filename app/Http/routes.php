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

$app->get('/', function () use ($app) {
    return $app->version();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api) {
    $api->group(['namespace' => 'App\Http\Controllers\Api\V1'], function($api) {

        $api->post('register', [
            'as' => 'register',
            'uses' => 'AuthController@register'
        ]);

        $api->post('auth', [
            'as' => 'auth',
            'uses' => 'AuthController@authenticate'
        ]);

        $api->group(['prefix' => 'user', 'middleware' =>['auth']], function($api) {
            $api->get('profile/me', [
                'as' => 'user.profile',
                'uses' => 'UserController@profile'
            ]);

            $api->get('profile/{id}', [
                'as' => 'user.profile',
                'uses' => 'UserController@profile'
            ]);
        });
    });
});

