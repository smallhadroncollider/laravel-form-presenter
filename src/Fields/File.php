<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\Request;
use Faker\Generator;

class File extends AbstractField implements FieldInterface
{
    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $this->setID();
        $this->setRequired();

        return $this->formBuilder->{$this->type}($this->name, $this->value(), $this->mergeAttrs($attrs));
    }

    public function request(Request $request, $data = [])
    {
        $data[$this->name] = $request->file($this->name);
        return $data;
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $test;
    }
}
