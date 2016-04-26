<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class Hidden extends AbstractField implements FieldInterface
{
    public function label($attrs = [])
    {
        return null;
    }

    public function display($attrs = [])
    {
        return $this->formBuilder->hidden($this->name, $this->value(), $attrs);
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $test->type($faker->word, $this->name);
    }
}
