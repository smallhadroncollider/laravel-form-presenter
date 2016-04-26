<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

interface FieldInterface
{
    public function name();
    public function type();
    public function value();
    public function test(TestCase $test, Generator $faker);
    public function label($attrs = []);
    public function display($attrs = []);
}
