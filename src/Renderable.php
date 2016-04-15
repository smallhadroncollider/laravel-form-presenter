<?php

namespace SmallHadronCollider\LaravelFormPresenter;

interface Renderable
{
    public function render();
    public function id();
    public function fieldNames(array $fieldNames = []);
    public function setData(array $data);
    public function hasFiles();
}
