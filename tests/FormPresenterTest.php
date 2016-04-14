<?php

use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;
use SmallHadronCollider\LaravelFormPresenter\FormPresenter;

class FormPresenterTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        $this->app["request"]->setSession($this->app["session"]->driver("array"));
        FieldPresenter::presenter(null);
    }

    public function testDisplay()
    {
        $form = new FormPresenter(new TestFieldSet);

        $this->assertEquals(
            '<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text"><label for="email">Email</label><input id="email" placeholder="Email" name="email" type="email">',
            $form->display()
        );
    }

    public function testToString()
    {
        $form = new FormPresenter(new TestFieldSet);

        $this->assertEquals(
            '<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text"><label for="email">Email</label><input id="email" placeholder="Email" name="email" type="email">',
            $form
        );
    }

    public function testExclude()
    {
        $form = new FormPresenter(new TestFieldSet);
        $form->exclude(["email"]);

        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $form->display());
    }

    public function testOnly()
    {
        $form = new FormPresenter(new TestFieldSet);
        $form->only(["name"]);

        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $form->display());
    }

    public function testFieldNames()
    {
        $form = new FormPresenter(new TestFieldSet);
        $this->assertEquals(["name", "email"], $form->fieldNames());
    }

    public function testPassthrough()
    {
        $form = new FormPresenter(new TestFieldSet);
        $button = $form->submit("Add", ["class" => "button"]);

        $this->assertEquals('<input class="button" type="submit" value="Add">', $button->toHtml());
    }
}

class TestFieldSet extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            $this->field([
                "type" => "text",
                "name" => "name",
                "label" => "Name",
            ]),
            $this->field([
                "type" => "email",
                "name" => "email",
                "label" => "Email",
            ]),
        ];
    }
}
