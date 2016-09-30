<?php

namespace SmallHadronCollider\LaravelFormPresenter;

use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestCase;

abstract class FieldSetPresenter implements Fieldlike
{
    static protected $indexes = [];

    // Resets multi-field indexes
    // Useful for testing
    static public function clearIndexes()
    {
        static::$indexes = [];
    }

    protected $exclude = [];
    protected $only = [];
    protected $model;
    protected $fields;

    public function form()
    {
        return new FormPresenter($this);
    }

    public function setModel($model)
    {
        $this->model = $model ;
        return $this;
    }

    public function set($key, $value)
    {
        if (!$this->model) {
            $this->model = (object) [];
        }

        $this->model->{$key} = $value;

        return $this;
    }

    public function render()
    {
        $content = array_reduce($this->getFields(), function ($html, Fieldlike $field) {
            $field->setModel($this->model);
            return $this->shouldRenderField($field) ? $html . $field->render() : $html;
        }, "");

        return $this->wrap($content);
    }

    public function hasFiles()
    {
        return !!count(array_filter($this->getFields(), function ($field) {
            return $field->hasFiles();
        }));
    }

    public function id()
    {
        return implode(",", $this->fieldNames());
    }

    protected function field(array $attrs)
    {
        return new FieldPresenter($attrs);
    }

    protected function multifield($parent, array $attrs)
    {
        return new FieldPresenter($attrs, $parent, $this->getIndex($parent));
    }

    public function exclude(array $exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }

    public function only(array $only)
    {
        $this->only = $only;
        return $this;
    }

    public function rules(array $rules = [])
    {
        $rules = array_reduce($this->getFields(), function ($rules, $field) {
            return $field->rules($rules);
        }, $rules);

        return $this->filterExcluded($rules);
    }

    public function fieldNames(array $fieldNames = [])
    {
        $fieldNames = array_reduce($this->getFields(), function ($fieldNames, $field) {
            return $field->fieldNames($fieldNames);
        }, $fieldNames);

        return array_values(array_filter($fieldNames, function ($fieldName) {
            return !in_array($fieldName, $this->exclude);
        }));
    }

    public function request(Request $request, array $data = [])
    {
        return array_reduce($this->flatFields(), function ($data, $field) use ($request) {
            return $field->request($request, $data);
        }, $data);
    }

    protected function getFields()
    {
        if (!$this->fields) {
            $this->fields = $this->transform($this->fields());
        }

        return $this->fields;
    }

    public function flatFields(array $fields = [])
    {
        $fields = array_reduce($this->getFields(), function ($fields, $field) {
            return $field->flatFields($fields);
        }, $fields);

        return $this->filterExcluded($fields);
    }

    public function populateTest($test)
    {
        $faker = Factory::create();

        foreach ($this->flatFields() as $field) {
            $field->test($test, $faker);
        }

        return $test;
    }

    protected function model($property)
    {
        if (!is_object($this->model)) {
            return null;
        }

        // Can't use isset/property_exists as it may not work on __get() properties
        try {
            return $this->model->{$property};
        } catch (Exception $e) {}

        return null;
    }

    protected function shouldRenderField(Fieldlike $field)
    {
        $excluded = in_array($field->id(), $this->exclude);
        $notIncluded = !empty($this->only) && !in_array($field->id(), $this->only);

        return !($excluded || $notIncluded);
    }

    protected function wrap($content)
    {
        return $content;
    }

    protected function filterExcluded(array $data)
    {
        $results = [];

        foreach ($data as $name => $rule) {
            if (!in_array($name, $this->exclude)) {
                $results[$name] = $rule;
            }
        }

        return $results;
    }

    protected function transform(array $fields)
    {
        return array_map(function ($field) {
            if (!is_array($field)) {
                return $field;
            }

            if (array_key_exists("parent", $field)) {
                return $this->multifield($field["parent"], $field);
            }

            return $this->field($field);
        }, $fields);
    }

    protected function getIndex($parent)
    {
        if (!array_key_exists($parent, static::$indexes)) {
            static::$indexes[$parent] = [
                "index" => 0,
                "previous" => $this,
            ];
        }

        if (static::$indexes[$parent]["previous"] !== $this) {
            static::$indexes[$parent]["index"]++;
            static::$indexes[$parent]["previous"] = $this;
        }

        return static::$indexes[$parent]["index"];
    }

    abstract protected function fields();
}
