<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use Closure;
use Collective\Html\FormBuilder;

class FormPresenter
{
    private $fieldset;
    private $attr;

    public function __construct(FieldSetPresenter $fieldset)
    {
        $this->fieldset = $fieldset;
        $this->formBuilder = app()->make(FormBuilder::class);
    }

    public function render()
    {
        return $this->fieldset->render();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function exclude(array $exclude)
    {
        $this->fieldset->exclude($exclude);
        return $this;
    }

    public function only(array $only)
    {
        $this->fieldset->only($only);
        return $this;
    }

    public function attr(array $attr = [])
    {
        $this->attr = $attr;
        return $this;
    }

    public function open(array $attr = [])
    {
        return $this->formBuilder->open(array_merge($this->attr, $attr));
    }

    public function response(array $response)
    {
        $fields = $this->fieldset->fieldNames();

        foreach ($response as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($response[$key]);
            }
        }

        return $response;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->formBuilder, $name], $arguments);
    }
}
