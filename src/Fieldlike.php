<?php

namespace SmallHadronCollider\LaravelFormPresenter;

interface Fieldlike
{
    public function render();
    public function id();
    public function fieldNames(array $fieldNames = []);
    public function rules(array $rules = []);
    public function flatFields(array $fields = []);
    public function setModel($model);
    public function hasFiles();
}
