<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

interface FieldInterface
{
    public function label($attrs = []);
    public function display($attrs = []);
}
