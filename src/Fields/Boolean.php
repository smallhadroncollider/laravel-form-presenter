<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Collective\Html\FormBuilder;

class Boolean extends Field
{
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

        $yes = $this->value() || $this->value() === null;

        return $this->formBuilder->radio($this->attr("name"), 1, $yes, ["id" => $this->attr("name") . "_yes"]) . " " . $this->formBuilder->label($this->attr("name") . "_yes", "Yes") . " " . $this->formBuilder->radio($this->attr("name"), 0, !$yes, ["id" => $this->attr("name") . "_no"]) . " " . $this->formBuilder->label($this->attr("name") . "_no", "No");
    }
}
