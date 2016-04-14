<?php

namespace SmallHadronCollider\LaravelFormPresenter\ModelPresenters;

use Illuminate\Support\Collection;
use SmallHadronCollider\LaravelFormPresenter\ModelPresenterInterface;

class EloquentModelPresenter implements ModelPresenterInterface
{
    public function present($model)
    {
        $data = [];

        foreach ($model->toArray() as $property => $value) {
            $collection = $value instanceof Collection;
            $data[$property] = $collection ? $value->pluck("id")->unique()->all() : $value;
        }

        return $data;
    }
}
