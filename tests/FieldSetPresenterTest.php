<?php

use SmallHadronCollider\LaravelFormPresenter\FormPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;
use SmallHadronCollider\LaravelFormPresenter\ModelPresenterInterface;

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
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $form->display());
    }

    public function testFieldNames()
    {
        $fieldset = new TestNestedFieldSetPresenter();
        $this->assertEquals(["email", "name"], $fieldset->fieldNames());
    }

    public function testFieldNamesExcluding()
    {
        $fieldset = new TestNestedFieldSetPresenter();
        $this->assertEquals(["name"], $fieldset->fieldNamesExcluding(["email"]));
    }

    public function testSetModel()
    {
        $this->app->bind(ModelPresenterInterface::class, TestModelPresenter::class);

        $fieldset = new TestFieldSetPresenter();
        $fieldset->setModel(new TestModel());

        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text" value="Test">', $fieldset->render());
    }

    public function testDynamicField()
    {
        $this->app->bind(ModelPresenterInterface::class, TestModelPresenter::class);

        // Field shouldn't render as it's determined by model
        $fieldset = new TestModelFieldSetPresenter();
        $this->assertEquals('', $fieldset->render());

        // Field should render
        $fieldset = new TestModelFieldSetPresenter();
        $fieldset->setModel(new TestModel());

        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text" value="Test">', $fieldset->render());
    }

    public function testRules()
    {
        $fieldset = new TestFieldSetPresenter();

        $this->assertEquals([
            "name" => ["required"],
        ], $fieldset->rules());

        $fieldset = new TestNestedFieldSetPresenter();

        $this->assertEquals([
            "email" => ["required", "email"],
            "name" => ["required"],
        ], $fieldset->rules());
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
                "rules" => ["required"],
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
                "rules" => ["required", "email"],
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

class TestModelFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        if ($this->model("name")) {
            return [
                $this->field([
                    "type" => "text",
                    "name" => "name",
                    "label" => "Name",
                ]),
            ];
        }

        return [];
    }
}

class TestModel
{
    public $attrs = [
        "name" => "Test",
    ];
}

class TestModelPresenter implements ModelPresenterInterface
{
    public function present($model)
    {
        return $model->attrs;
    }
}
