<?php

namespace SmallHadronCollider\LaravelFormPresenter;

interface Renderable
{
    public function render();
    public function id();
    public function setData(array $data);
}
