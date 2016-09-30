<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

interface FieldInterface
{
    public function name();
    public function type();
    public function value();
    public function request(Request $request, $data = []);
    public function test(TestCase $test, Generator $faker);
    public function label($attrs = []);
    public function display($attrs = []);
}
