<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class Select extends AbstractField implements FieldInterface
{
    protected $items = [];

    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $attrs = $this->setID($attrs);
        $attrs = $this->setRequired($attrs);

        return $this->formBuilder->select($this->name, $this->items, $this->value(), $attrs);
    }

    protected function setup(array $attr)
    {
        $attr = parent::setup($attr);
        $this->items = array_get($attr, "items", []);

        return $attr;
    }
}
