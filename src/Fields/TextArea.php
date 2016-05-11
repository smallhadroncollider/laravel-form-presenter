<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class TextArea extends AbstractField implements FieldInterface
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

        return $this->formBuilder->textarea($this->name, $this->value(), $this->mergeAttrs($attrs));
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $test->type(implode(" ", $faker->words()), $this->name);
    }
}
