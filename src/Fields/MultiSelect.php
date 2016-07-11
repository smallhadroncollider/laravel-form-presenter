<?php

namespace SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class MultiSelect extends Select implements FieldInterface
{
    protected $collectionClasses = [
        "Illuminate\Support\Collection",
        "Analogue\ORM\System\Proxies\CollectionProxy",
    ];

    protected function isCollection($value)
    {
        return array_reduce($this->collectionClasses, function ($collection, $class) use ($value) {
            return $collection || $value instanceof $class;
        }, false);
    }

    public function setValue($value)
    {
        $this->value = $this->isCollection($value) ? $value->pluck("id")->unique()->all() : $value;
        return $this;
    }

    public function display($attrs = [])
    {
        $this->setID();
        $this->setRequired();
        $this->setAttr("multiple", "true");

        return $this->formBuilder->select("{$this->name}[]", $this->items, $this->value(), $this->mergeAttrs($attrs));
    }

    public function test(TestCase $test, Generator $faker)
    {
        $values = array_filter(array_keys($this->items), function ($value) {
            return !!$value;
        });

        if (count($values)) {
            $test->select($faker->randomElements($values), "{$this->name}");
        }

        return true;
    }
}
