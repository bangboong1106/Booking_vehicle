<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Validators\Base\CustomValidator;
use Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::resolver(function($translator, $data, $rules,
                                     $messages = array(), $customAttributes = array()) {
            return new CustomValidator($translator, $data, $rules,
                $messages, $customAttributes);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}