<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Exception;
use Collective\Html\FormBuilder;

abstract class AbstractField
{
    protected $name;
    protected $label;
    protected $type;
    protected $value;

    public function __construct(array $attr)
    {
        $this->formBuilder = app()->make(FormBuilder::class);
        $this->setup($attr);
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function name()
    {
        return $this->name;
    }

    public function type()
    {
        return $this->type;
    }

    public function __toString()
    {
        return $this->display();
    }

    protected function setup(array $attr)
    {
        $this->checkAttr($attr);

        foreach (["name", "type", "label", "value"] as $property) {
            $this->{$property} = array_get($attr, $property);
        }

        return $attr;
    }

    public function value()
    {
        return old($this->name, $this->value);
    }

    abstract public function display();
    abstract public function label();

    protected function checkAttr(array $attr)
    {
        foreach (["type", "name", "label"] as $property) {
            if (!array_key_exists($property, $attr)) {
                throw new Exception("{$property} property missing");
            }
        }
    }
}
