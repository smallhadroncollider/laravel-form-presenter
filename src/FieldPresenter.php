<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use ReflectionClass;
use Closure;
use Exception;

use SmallHadronCollider\LaravelFormPresenter\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

class FieldPresenter implements Fieldlike
{
    /**
     * Static Methods
     **/
    protected static $presenterResolver;

    protected static $types = [
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
        "multi-select" => Fields\MultiSelect::class,
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
    protected $field;
    protected $name;

    public function __construct(array $attr, $parent = null, $id = 0)
    {
        $this->name = array_get($attr, "name");

        if ($attr["type"] == "fish") {
            dd($attr);
        }

        if ($parent) {
            $attr["name"] = "{$parent}[{$id}][{$attr["name"]}]";
        }

        // Update to user array type naming
        $this->field = $this->getField($attr);
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

    public function id()
    {
        return $this->field->name();
    }

    public function test(TestCase $test, Generator $faker)
    {
        return $this->field->test($test, $faker);
    }

    public function rules(array $rules = [])
    {
        $fieldRules = $this->field->rules();

        if ($fieldRules) {
            $rules[$this->id()] = $fieldRules;
        }

        return $rules;
    }

    public function fieldNames(array $fieldNames = [])
    {
        $fieldNames[] = $this->id();
        return $fieldNames;
    }

    public function flatFields(array $fields = [])
    {
        $fields[$this->id()] = $this;
        return $fields;
    }

    public function setModel($model)
    {
        if (!is_object($model)) {
            return $this;
        }

        $property = $this->name;

        // Can't use isset/property_exists as it may not work on __get() properties
        try {
            $this->field->setValue($model->{$property});
        } catch (Exception $e) {
            $this->field->setValue(null);
        }

        return $this;
    }

    protected function getPresenterResolver()
    {
        if (self::$presenterResolver) {
            return self::$presenterResolver;
        }

        return function ($attrs) {
            return "{$attrs["field"]->label()}{$attrs["field"]->display()}";
        };
    }

    protected function getField(array $attr)
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
