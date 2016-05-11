<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class Field extends AbstractField implements FieldInterface
{
    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $this->setID();
        $this->setPlaceholder();
        $this->setRequired();

        return $this->formBuilder->{$this->type}($this->name, $this->value(), $this->mergeAttrs($attrs));
    }

    public function test(TestCase $test, Generator $faker)
    {
        switch ($this->type) {
            case "date":
                return $test->type(date("Y-m-d"), $this->name);
            case "datetime":
                return $test->type(date("Y-m-d G:i:s"), $this->name);
            case "number":
                return $test->type(mt_rand(1, 10), $this->name);
            case "email":
                return $test->type($faker->email, $this->name);
            case "password":
                return $test->type($faker->password, $this->name);
            default:
                return $test->type(ucwords($faker->word), $this->name);
        }
    }
}
