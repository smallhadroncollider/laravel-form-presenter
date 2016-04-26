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
        $attrs = $this->setID($attrs);
        $attrs = $this->setPlaceholder($attrs);
        $attrs = $this->setRequired($attrs);

        return $this->formBuilder->{$this->type}($this->name, $this->value(), $attrs);
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $test->type(ucwords($faker->word), $this->name);
    }
}
