<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class Hidden extends Field implements FieldInterface
{
    public function label($attrs = [])
    {
        return null;
    }

    public function display($attrs = [])
    {
        return $this->formBuilder->hidden($this->attr("name"), $this->value(), $attrs);
    }
}
