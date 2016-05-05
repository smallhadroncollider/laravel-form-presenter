<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class FormBuilderProvider
{
    private $formBuilder;

    public function __construct()
    {
        $this->formBuilder = new FormBuilder(
            app()->make(HtmlBuilder::class),
            app()->make(UrlGenerator::class),
            app()->make(ViewFactory::class),
            csrf_token()
        );
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->formBuilder, $name], $arguments);
    }
}
