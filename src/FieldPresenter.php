<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use ReflectionClass;
use Closure;
use Exception;
use Collective\Html\FormBuilder;
use SmallHadronCollider\LaravelFormPresenter\Fields;

class FieldPresenter implements Fieldlike
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
    private $field;
    private $formBuilder;

    public function __construct(array $attr)
    {
        $this->field = $this->getField($attr);
        $this->formBuilder = app()->make(FormBuilder::class);
    }

    public function render()
    {
        $presenter = $this->getPresenterResolver();

        return $presenter([
            "field" => $this->field,
        ]);
    }

    public function hasFiles()
    {
        return $this->field->type() == "file";
    }

    public function __toString()
    {
        return $this->render();
    }

    public function id()
    {
        return $this->field->name();
    }

    public function rules(array $rules = [])
    {
        $rules[$this->field->name()] = $this->field->rules();
        return $rules;
    }

    public function fieldNames(array $fieldNames = [])
    {
        $fieldNames[] = $this->id();
        return $fieldNames;
    }

    public function setData(array $data)
    {
        $id = $this->id();

        if (array_key_exists($id, $data)) {
            $this->field->setValue($data[$id]);
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

    private function getField(array $attr)
    {
        $type = array_get($attr, "type");

        if (!array_key_exists($type, static::$types)) {
            $allowed = implode(", ", array_keys(static::$types));
            throw new Exception("Invalid type: {$type} (allowed types: {$allowed})");
        }

        $typeClass = static::$types[$type];

        return new $typeClass($attr);
    }
}
