<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class Hidden extends AbstractField implements FieldInterface
{
    public function label($attrs = [])
    {
        return null;
    }

    public function display($attrs = [])
    {
        return $this->formBuilder->hidden($this->name, $this->value(), $attrs);
    }
}
