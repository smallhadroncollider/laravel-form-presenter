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
        $attrs = $this->setID($attrs);
        $attrs = $this->setPlaceholder($attrs);
        $attrs = $this->setRequired($attrs);

        return $this->formBuilder->textarea($this->name, $this->value(), $attrs);
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $test->type(implode(" ", $faker->words()), $this->name);
    }
}
