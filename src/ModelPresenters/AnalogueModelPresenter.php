<?php

namespace SmallHadronCollider\LaravelFormPresenter\ModelPresenters;

use Illuminate\Support\Collection;
use Analogue\System\CollectionProxy;
use SmallHadronCollider\LaravelFormPresenter\ModelPresenterInterface;

class AnalogueModelPresenter implements ModelPresenterInterface
{
    public function present($model)
    {
        $data = [];

        foreach ($model->getEntityAttributes() as $property => $value) {
            $collection = $value instanceof Collection || $value instanceof CollectionProxy;
            $data[$property] = $collection ? $value->pluck("id")->unique()->all() : $value;
        }

        return $data;
    }
}
