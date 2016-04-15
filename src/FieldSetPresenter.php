<?php

namespace SmallHadronCollider\LaravelFormPresenter;

abstract class FieldSetPresenter implements Renderable
{
    protected $exclude = [];
    protected $only = [];
    protected $model = [];
    protected $fields;

    public function form()
    {
        return new FormPresenter($this);
    }

    /**
     * Turns model into an array structure and then stores it
     */
    public function setModel($model)
    {
        $presenter = app()->make(ModelPresenterInterface::class);
        $this->model = $presenter->present($model);
        return $this;
    }

    /**
     * If model is already in array form (i.e. when fieldset is nested)
     */
    public function setData(array $data)
    {
        $this->model = $data;
        return $this;
    }

    public function render()
    {
        $content = array_reduce($this->getFields(), function ($html, Renderable $field) {
            $field->setData($this->model);
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

    public function __toString()
    {
        return $this->render();
    }

    public function id()
    {
        return implode(",", $this->fieldNames());
    }

    public function field(array $attrs)
    {
        return new FieldPresenter($attrs);
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

    public function fieldNames(array $fieldNames = [])
    {
        return array_reduce($this->getFields(), function ($fieldNames, $field) {
            return $field->fieldNames($fieldNames);
        }, $fieldNames);
    }

    public function fieldNamesExcluding(array $excluding)
    {
        return array_values(array_filter($this->fieldNames(), function ($fieldName) use ($excluding) {
            return !in_array($fieldName, $excluding);
        }));
    }

    protected function getFields()
    {
        if (!$this->fields) {
            $this->fields = $this->fields();
        }

        return $this->fields;
    }

    protected function model($attr)
    {
        return array_key_exists($attr, $this->model) ? $this->model[$attr] : null;
    }

    protected function shouldRenderField(Renderable $field)
    {
        $excluded = in_array($field->id(), $this->exclude);
        $notIncluded = !empty($this->only) && !in_array($field->id(), $this->only);

        return !($excluded || $notIncluded);
    }

    protected function wrap($content)
    {
        return $content;
    }

    abstract protected function fields();
}
