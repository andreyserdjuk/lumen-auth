<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Router;

Controller::buildResponseUsing(function ($request, $errors) {
    return new JsonResponse(['errors' => $errors], 422);
});

/** @var Router $router */
$router->post('/register', ['uses' => 'RegistrationController@accept', 'as' => 'accept']);
$router->get('/activate/{token}', ['uses' => 'RegistrationController@activate', 'as' => 'activate']);
$router->post('/auth', ['uses' => 'AuthenticationController@auth', 'as' => 'auth']);
$router->get('/verify/{token}', ['uses' => 'AuthenticationController@verify', 'as' => 'verify']);
