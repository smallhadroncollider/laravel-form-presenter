<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

class TextArea extends AbstractField implements FieldInterface
{
    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $attrs = array_merge([
            "id" => $this->name,
            "placeholder" => $this->label,
        ], $attrs);

        return $this->formBuilder->textarea($this->name, $this->value(), $attrs);
    }
}
