<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class FormBuilderProvider
{
    private static $formBuilder;

    public static function clear()
    {
        static::$formBuilder = null;
    }

    private function generate()
    {
        return new FormBuilder(
            app()->make(HtmlBuilder::class),
            app()->make(UrlGenerator::class),
            app()->make(ViewFactory::class),
            csrf_token()
        );
    }

    public function __construct()
    {
        if (!static::$formBuilder) {
            static::$formBuilder = $this->generate();
        }
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([static::$formBuilder, $name], $arguments);
    }
}
