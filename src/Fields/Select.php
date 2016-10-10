<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class Select extends AbstractField implements FieldInterface
{
    protected $items = [];

    public function setValue($value)
    {
        $this->value = is_object($value) ? $value->id : $value;
        return $this;
    }

    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $this->setID();
        $this->setRequired();

        if (array_key_exists("readonly", $this->attrs)) {
            $value = null;

            if (array_key_exists($this->value(), $this->items)) {
                $value = $this->items[$this->value()];
            }

            return $this->formBuilder->text("{$this->name}_visible", $value, $this->mergeAttrs($attrs)) . $this->formBuilder->hidden($this->name, $this->value(), $this->mergeAttrs($attrs));
        }

        return $this->formBuilder->select($this->name, $this->items, $this->value(), $this->mergeAttrs($attrs));
    }

    protected function setup(array $attr)
    {
        $attr = parent::setup($attr);
        $this->items = array_get($attr, "items", []);

        return $attr;
    }

    public function test(TestCase $test, Generator $faker)
    {
        $value = $faker->randomElement(array_filter(array_keys($this->items)), function ($value) {
            return !!$value;
        });

        return $test->select($value, $this->name);
    }
}
