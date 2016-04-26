<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class Field extends AbstractField implements FieldInterface
{
    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $attrs = $this->setID($attrs);
        $attrs = $this->setPlaceholder($attrs);
        $attrs = $this->setRequired($attrs);

        return $this->formBuilder->{$this->type}($this->name, $this->value(), $attrs);
    }
}
