<?php

use SmallHadronCollider\LaravelFormPresenter\FormPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;

class FieldSetPresenterTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        $this->app["request"]->setSession($this->app["session"]->driver("array"));
        FieldPresenter::presenter(null);
    }

    public function testBasic()
    {
        $fieldset = new TestFieldSetPresenter();
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $fieldset->render());
    }

    public function testToString()
    {
        $fieldset = new TestFieldSetPresenter();
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $fieldset);
    }

    public function testNested()
    {
        $fieldset = new TestNestedFieldSetPresenter();
        $this->assertEquals('<label for="email">Email</label><input id="email" placeholder="Email" name="email" type="email"><label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $fieldset->render());
    }

    public function testWrapped()
    {
        $fieldset = new TestViewFieldSetPresenter();
        $this->assertEquals('<fieldset class="form-group"><label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text"></fieldset>', $fieldset->render());
    }

    public function testForm()
    {
        $form = (new TestFieldSetPresenter())->form();

        $this->assertInstanceOf(FormPresenter::class, $form);
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $form->render());
    }
}

class TestFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            $this->field([
                "type" => "text",
                "name" => "name",
                "label" => "Name",
            ]),
        ];
    }
}

class TestNestedFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            $this->field([
                "type" => "email",
                "name" => "email",
                "label" => "Email",
            ]),
            new TestFieldSetPresenter(),
        ];
    }
}

class TestViewFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            $this->field([
                "type" => "text",
                "name" => "name",
                "label" => "Name",
            ]),
        ];
    }

    protected function wrap($content)
    {
        return '<fieldset class="form-group">'. $content . '</fieldset>';
    }
}
