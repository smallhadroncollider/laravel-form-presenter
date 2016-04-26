<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class Select extends AbstractField implements FieldInterface
{
    protected $items = [];

    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $attrs = $this->setID($attrs);
        $attrs = $this->setRequired($attrs);

        return $this->formBuilder->select($this->name, $this->items, $this->value(), $attrs);
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
