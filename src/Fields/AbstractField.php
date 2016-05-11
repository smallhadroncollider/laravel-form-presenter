<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Exception;
use BadMethodCallException;
use SmallHadronCollider\LaravelFormPresenter\FormBuilderProvider;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

abstract class AbstractField
{
    protected $name;
    protected $label;
    protected $type;
    protected $value;
    protected $attrs;
    protected $properties;

    protected $rules = [];

    protected $appendableAttributes = ["class"];


    public function __construct(array $attr)
    {
        $this->formBuilder = new FormBuilderProvider();
        $this->properties = $this->setup($attr);
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

    public function rules()
    {
        return $this->rules ? : [];
    }

    public function __toString()
    {
        return $this->display();
    }

    protected function setup(array $properties)
    {
        $this->checkAttr($properties);

        foreach (["name", "type", "label", "value", "rules", "attrs"] as $property) {
            $this->{$property} = array_get($properties, $property);
        }

        return $properties;
    }

    public function value()
    {
        return old($this->name, $this->value);
    }

    abstract public function display();
    abstract public function label();
    abstract public function test(TestCase $test, Generator $faker);

    protected function checkAttr(array $property)
    {
        foreach (["type", "name", "label"] as $type) {
            if (!array_key_exists($type, $property)) {
                throw new Exception("{$type} property missing");
            }
        }
    }

    protected function setRequired()
    {
        $rules = $this->rules ? : [];

        if (in_array("required", $rules)) {
            $this->setAttr("required", "true");
        }

        return $this->attrs;
    }

    protected function setPlaceholder()
    {
        $this->setAttr("placeholder", $this->label);
        return $this->attrs;
    }

    protected function setID()
    {
        $this->setAttr("id", $this->name);
        return $this->attrs;
    }

    protected function setAttr($name, $value)
    {
        if (in_array($name, $this->appendableAttributes)) {
            return $this->appendAttr($name, $value);
        }

        $this->attrs[$name] = $value;
        return $this->attrs;
    }

    protected function appendAttr($name, $value)
    {
        $current = array_get($this->attrs, $name);
        $this->attrs[$name] = $current ? "{$current} {$value}" : $value;
        return $this->attrs;
    }

    protected function mergeAttrs($attrs)
    {
        foreach ($attrs as $key => $value) {
            $this->setAttr($key, $value);
        }

        return $this->attrs;
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        return null;
    }
}
