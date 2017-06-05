<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class Checked extends AbstractField implements FieldInterface
{
    protected $valueAttr;

    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    public function display($attrs = [])
    {
        $checked = $this->value() == $this->valueAttr;
        return $this->formBuilder->{$this->type}($this->name, $this->valueAttr, $checked, $this->mergeAttrs($attrs));
    }

    protected function setup(array $attr)
    {
        $attr = parent::setup($attr);
        $this->valueAttr = array_get($attr, "value");

        return $attr;
    }

    public function request(Request $request, $data = [])
    {
        $data[$this->name] = $request->get($this->name) ? true : false;
        return $data;
    }

    public function test(TestCase $test, Generator $faker)
    {
        return mt_rand(0, 1) ? $test->check($this->name) : $test->uncheck($this->name);
    }
}
