<?php

namespace SmallHadronCollider\LaravelFormPresenter;

abstract class FieldSetPresenter implements Renderable
{
    protected $exclude = [];
    protected $only = [];

    public function form()
    {
        return new FormPresenter($this);
    }

    public function render()
    {
        $content = array_reduce($this->fields(), function ($html, Renderable $field) {
            return $this->shouldRenderField($field) ? $html . $field->render() : $html;
        }, "");

        return $this->wrap($content);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function id()
    {
        return "";
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

    public function fieldNames()
    {
        return array_map(function ($field) {
            return $field->id();
        }, $this->fields());
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
