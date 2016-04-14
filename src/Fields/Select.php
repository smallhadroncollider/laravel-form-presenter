<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class Select extends Field implements FieldInterface
{
    public function display($attrs = [])
    {
        $attrs = array_merge(["id" => $this->attr("name")], $attrs);
        return $this->formBuilder->select($this->attr("name"), $this->attr("items", []), $this->value(), $attrs);
    }
}
