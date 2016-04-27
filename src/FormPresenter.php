<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use Closure;
use Collective\Html\FormBuilder;

class FormPresenter
{
    private $fieldset;
    private $fields;
    private $attr = [];

    public function __construct(FieldSetPresenter $fieldset)
    {
        $this->fieldset = $fieldset;
        $this->formBuilder = app()->make(FormBuilder::class);
    }

    public function display()
    {
        return $this->fieldset->render();
    }

    public function __toString()
    {
        return $this->display();
    }

    public function exclude(array $exclude)
    {
        $this->fieldset->exclude($exclude);
        return $this;
    }

    public function only(array $only)
    {
        $this->fieldset->only($only);
        return $this;
    }

    public function attr(array $attr = [])
    {
        $this->attr = $attr;
        return $this;
    }

    public function open(array $attr = [])
    {
        if ($this->fieldset->hasFiles()) {
            $this->attr["files"] = true;
        }

        return $this->formBuilder->open(array_merge($this->attr, $attr));
    }

    public function fieldNames()
    {
        return $this->fieldset->fieldNames();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->formBuilder, $name], $arguments);
    }

    public function __get($name)
    {
        $fields = $this->getFields();
        return array_key_exists($name, $fields) ? $fields[$name]->render() : "";
    }

    protected function getFields()
    {
        if (!$this->fields) {
            $this->fields = $this->fieldset->flatFields();
        }

        return $this->fields;
    }
}
