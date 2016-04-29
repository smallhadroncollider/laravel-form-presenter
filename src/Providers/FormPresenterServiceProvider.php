<?php

namespace SmallHadronCollider\LaravelFormPresenter\Providers;

use Illuminate\Support\ServiceProvider;

class FormPresenterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->addNamespace("form-presenter", __DIR__ . "/../templates");
    }

    public function register() {}
}
