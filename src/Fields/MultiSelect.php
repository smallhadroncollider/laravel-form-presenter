<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class MultiSelect extends Select implements FieldInterface
{
    public function setValue($value)
    {
        $collection = $value instanceof Collection;
        $this->value = $collection ? $value->pluck("id")->unique()->all() : $value;
        return $this;
    }

    public function display($attrs = [])
    {
        $attrs = $this->setID($attrs);
        $attrs = $this->setRequired($attrs);
        $attrs["multiple"] = "true";

        return $this->formBuilder->select("{$this->name}[]", $this->items, $this->value(), $attrs);
    }

    public function test(TestCase $test, Generator $faker)
    {
        $values = $faker->randomElements(array_filter(array_keys($this->items)), function ($value) {
            return !!$value;
        });

        return $test->select($values, "{$this->name}[]");
    }
}
