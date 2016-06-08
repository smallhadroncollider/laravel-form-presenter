<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class Password extends Field implements FieldInterface
{
    public function display($attrs = [])
    {
        $this->setID();
        $this->setPlaceholder();
        $this->setRequired();

        return $this->formBuilder->{$this->type}($this->name, $this->mergeAttrs($attrs));
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $test->type($faker->password, $this->name);
    }
}
