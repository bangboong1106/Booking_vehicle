<?php

namespace App\Providers;

use App\Helpers\MyForm;
use Collective\Html\HtmlBuilder;
use Collective\Html\HtmlServiceProvider;

/**
 * Class FormServiceProvider
 * @package App\Providers
 */
class FormServiceProvider extends HtmlServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     *
     */
    public function registerFormBuilder()
    {
        $this->app->singleton('myForm', function ($app) {
            $form = new MyForm($app['html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);
            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['html', 'myForm', HtmlBuilder::class, MyForm::class];
    }
}
