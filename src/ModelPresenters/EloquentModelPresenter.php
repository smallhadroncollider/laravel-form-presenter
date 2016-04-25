<?php

namespace SmallHadronCollider\LaravelFormPresenter\ModelPresenters;

use Illuminate\Database\Eloquent\Collection;
use SmallHadronCollider\LaravelFormPresenter\ModelPresenterInterface;

class EloquentModelPresenter implements ModelPresenterInterface
{
    public function present($model)
    {
        $data = [];

        foreach ($model as $property => $value) {
            $collection = $value instanceof Collection;
            $data[$property] = $collection ? $value->pluck("id")->unique()->all() : $value;
        }

        return $data;
    }
}
