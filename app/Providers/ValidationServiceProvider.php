<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Validation\Factory;
use LumenAuth\Core\RegistrationAcceptor;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot()
    {
//        /** @var Factory $validator */
//        $validator = $this->app->get(Factory::class);
//        $validator->extend('uemail', function($attribute, $value, $parameters) {
//            $registrationAcceptor = $this->app->get(RegistrationAcceptor::class);
//            return $registrationAcceptor->isEmailPresent($value);
//        });
        Validator::extend('unique_email', function($attribute, $value, $parameters) {
            $registrationAcceptor = $this->app->get(RegistrationAcceptor::class);
            return !$registrationAcceptor->isEmailPresent($value);
        }, 'Given email is not unique.');
    }
}
