<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class Checked extends AbstractField implements FieldInterface
{
    protected $valueAttr;

    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $checked = $this->value() == $this->valueAttr;
        return $this->formBuilder->{$this->type}($this->name, $this->valueAttr, $checked, $attrs);
    }

    protected function setup(array $attr)
    {
        $attr = parent::setup($attr);
        $this->valueAttr = array_get($attr, "value");

        return $attr;
    }
}
