<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Collective\Html\FormBuilder;

class Checked extends Field
{
    public function display($attrs = [])
    {
        $checked = $this->value() == $this->attr("value");
        return $this->formBuilder->{$this->attr("type")}($this->attr("name"), $this->attr("value"), $checked, $attrs);
    }
}
