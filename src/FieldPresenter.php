<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use Closure;
use Exception;
use Collective\Html\FormBuilder;
use SmallHadronCollider\LaravelFormPresenter\Fields;

class FieldPresenter implements Renderable
{
    /**
     * Static Methods
     **/
    private static $presenterResolver;

    public static function presenter(Closure $presenterResolver = null)
    {
        self::$presenterResolver = $presenterResolver;
    }

    /**
     * Instance Methods
     **/
    private $attr;
    private $field;

    private $formBuilder;

    private $types = [
        "text" => Fields\Field::class,
        "email" => Fields\Field::class,
        "number" => Fields\Field::class,
        "password" => Fields\Field::class,
        "date" => Fields\Field::class,
        "datetime" => Fields\Field::class,
        "hidden" => Fields\Hidden::class,
        "file" => Fields\Field::class,
        "textarea" => Fields\TextArea::class,
        "select" => Fields\Select::class,
    ];

    public function __construct(array $attr)
    {
        $this->attr = $this->getAttributes($attr);
        $this->field = $this->getField($attr);

        $this->formBuilder = app()->make(FormBuilder::class);
    }

    public function render()
    {
        $presenter = $this->getPresenterResolver();

        return $presenter([
            "field" => $this->field,
            "name" => $this->attr["name"],
            "type" => $this->attr["type"],
            "label" => $this->attr["label"],
        ]);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function id()
    {
        return $this->attr["name"];
    }

    public function setData(array $data)
    {
        $name = $this->attr["name"];

        if (array_key_exists($name, $data)) {
            $this->field->setValue($data[$name]);
        }

        return $this;
    }

    private function getPresenterResolver()
    {
        if (self::$presenterResolver) {
            return self::$presenterResolver;
        }

        return function ($attrs) {
            return "{$attrs["field"]->label()}{$attrs["field"]->display()}";
        };
    }

    private function getAttributes(array $attr)
    {
        foreach (["type", "name", "label"] as $property) {
            if (!array_key_exists($property, $attr)) {
                throw new Exception("{$property} property missing");
            }
        }

        return $attr;
    }

    private function getField(array $attr)
    {
        if (!array_key_exists($attr["type"], $this->types)) {
            $allowed = implode(", ", array_keys($this->types));
            throw new Exception("Invalid type: {$attr["type"]} (allowed types: {$allowed})");
        }

        $type = $this->types[$attr["type"]];

        return new $type($attr);
    }
}
