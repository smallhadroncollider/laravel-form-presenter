<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

interface FieldInterface
{
    public function name();
    public function type();
    public function value();
    public function label($attrs = []);
    public function display($attrs = []);
}
