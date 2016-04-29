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
    protected $attr;

    protected $rules = [];

    public function __construct(array $attr)
    {
        $this->formBuilder = new FormBuilderProvider();
        $this->attr = $this->setup($attr);
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

    protected function setup(array $attr)
    {
        $this->checkAttr($attr);

        foreach (["name", "type", "label", "value", "rules"] as $property) {
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
    abstract public function test(TestCase $test, Generator $faker);

    protected function checkAttr(array $attr)
    {
        foreach (["type", "name", "label"] as $property) {
            if (!array_key_exists($property, $attr)) {
                throw new Exception("{$property} property missing");
            }
        }
    }

    protected function setRequired($attr = [])
    {
        $rules = $this->rules ? : [];

        if (in_array("required", $rules)) {
            $attr["required"] = "true";
        }

        return $attr;
    }

    protected function setPlaceholder($attr = [])
    {
        $attr["placeholder"] = $this->label;
        return $attr;
    }

    protected function setID($attr = [])
    {
        $attr["id"] = $this->name;
        return $attr;
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->attr)) {
            return $this->attr[$name];
        }

        return null;
    }
}
