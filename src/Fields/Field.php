<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Collective\Html\FormBuilder;

class Field
{
    protected $attr = [];

    public function __construct(array $attr)
    {
        $this->formBuilder = app()->make(FormBuilder::class);
        $this->attr = $attr;
    }

    public function setValue($value)
    {
        $this->attr["value"] = $value;
        return $this;
    }

    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->attr("name"), $this->attr("label"), $attrs);
    }

    public function display($attrs = [])
    {
        $attrs = array_merge([
            "id" => $this->attr("name"),
            "placeholder" => $this->attr("label"),
        ], $attrs);

        return $this->formBuilder->{$this->attr("type")}($this->attr("name"), $this->value(), $attrs);
    }

    public function __toString()
    {
        return $this->display();
    }

    protected function attr($property, $default = null)
    {
        return array_key_exists($property, $this->attr) ? $this->attr[$property] : $default;
    }

    protected function value()
    {
        return old($this->attr("name"), $this->attr("value", null));
    }
}
