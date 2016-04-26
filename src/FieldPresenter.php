<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use ReflectionClass;
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

    private static $types = [
        "text" => Fields\Field::class,
        "checkbox" => Fields\Checked::class,
        "radio" => Fields\Checked::class,
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

    public static function presenter(Closure $presenterResolver = null)
    {
        self::$presenterResolver = $presenterResolver;
    }

    public static function add($type, $class)
    {
        if (!(new ReflectionClass($class))->implementsInterface(Fields\FieldInterface::class)) {
            throw new Exception("Field Type must implement FieldInterface");
        }

        static::$types[$type] = $class;
    }

    /**
     * Instance Methods
     **/
    private $attr;
    private $field;

    private $formBuilder;

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

    public function hasFiles()
    {
        return $this->attr["type"] == "file";
    }

    public function __toString()
    {
        return $this->render();
    }

    public function id()
    {
        return $this->attr["name"];
    }

    public function fieldNames(array $fieldNames = [])
    {
        $fieldNames[] = $this->id();
        return $fieldNames;
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
        if (!array_key_exists($attr["type"], static::$types)) {
            $allowed = implode(", ", array_keys(static::$types));
            throw new Exception("Invalid type: {$attr["type"]} (allowed types: {$allowed})");
        }

        $type = static::$types[$attr["type"]];

        return new $type($attr);
    }
}
