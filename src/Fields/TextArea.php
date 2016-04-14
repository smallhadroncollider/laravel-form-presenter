<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class TextArea extends Field implements FieldInterface
{
    public function display($attrs = [])
    {
        $attrs = array_merge([
            "id" => $this->attr("name"),
            "placeholder" => $this->attr("label"),
        ], $attrs);

        return $this->formBuilder->textarea($this->attr("name"), $this->value(), $attrs);
    }
}
