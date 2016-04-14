<?php

namespace SmallHadronCollider\LaravelFormPresenter\Providers;

use Illuminate\Support\ServiceProvider;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class FormPresenterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->addNamespace("smallhadroncollider/laravel-form-presenter", __DIR__ . "/../templates");
    }

    public function register()
    {
        $this->app->singleton(FormBuilder::class, function ($app) {
            return new FormBuilder(
                $app->make(HtmlBuilder::class),
                $app->make(UrlGenerator::class),
                $app->make(ViewFactory::class),
                csrf_token()
            );
        });
    }
}
