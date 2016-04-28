<?php

use SmallHadronCollider\LaravelFormPresenter\FormPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;
use SmallHadronCollider\LaravelFormPresenter\Fields\AbstractField;
use SmallHadronCollider\LaravelFormPresenter\PubliciseFormMethods;

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
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text">', $fieldset->render());
    }

    public function testNested()
    {
        $fieldset = new TestNestedFieldSetPresenter();
        $this->assertEquals('<label for="email">Email</label><input id="email" placeholder="Email" required="true" name="email" type="email"><label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text">', $fieldset->render());
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
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text">', $form->display());
    }

    public function testFieldNames()
    {
        $fieldset = new TestNestedFieldSetPresenter();
        $this->assertEquals(["email", "name"], $fieldset->fieldNames());
    }

    public function testFieldNamesExcluding()
    {
        $fieldset = new TestNestedFieldSetPresenter();
        $fieldset->exclude(["email"]);
        $this->assertEquals(["name"], $fieldset->fieldNames());
    }

    public function testSetModel()
    {
        $fieldset = new TestFieldSetPresenter();
        $fieldset->setModel(new TestModel());

        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text" value="Test">', $fieldset->render());

        $fieldset->setModel((object) []);
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text">', $fieldset->render());
    }

    public function testSetMagicModel()
    {
        $fieldset = new TestFieldSetPresenter();
        $fieldset->setModel(new TestMagicModel());

        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text" value="Test">', $fieldset->render());
    }

    public function testDynamicField()
    {
        // Field shouldn't render - it's determined by model
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


        // Test nested rules
        $fieldset = new TestNestedFieldSetPresenter();

        $this->assertEquals([
            "email" => ["required", "email"],
            "name" => ["required"],
        ], $fieldset->rules());


        // Test excluded fields are ignored
        $fieldset = new TestNestedFieldSetPresenter();
        $fieldset->exclude(["name"]);

        $this->assertEquals([
            "email" => ["required", "email"],
        ], $fieldset->rules());


        // Test with no rules
        $fieldset = new TestNoRulesFieldSetPresenter();
        $this->assertEquals([], $fieldset->rules());
    }

    public function testTest()
    {
        $fieldset = new TestFieldSetPresenter();
        $test = new PublicisedTestCase();

        $fieldset->populateTest($test);

        $this->assertRegExp("/^[A-Z][a-z]+$/", $test->arguments[0]);
        $this->assertEquals("name", $test->arguments[1]);
    }
}

class PublicisedTestCase extends TestCase
{
    public $arguments;

    public function __call($name, $arguments)
    {
        if ($name === "type") {
            $this->arguments = $arguments;
        }
    }
}

class TestFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "text",
                "name" => "name",
                "label" => "Name",
                "rules" => ["required"],
            ],
        ];
    }
}

class TestNestedFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "email",
                "name" => "email",
                "label" => "Email",
                "rules" => ["required", "email"],
            ],
            new TestFieldSetPresenter(),
        ];
    }
}

class TestViewFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "text",
                "name" => "name",
                "label" => "Name",
            ],
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
                [
                    "type" => "text",
                    "name" => "name",
                    "label" => "Name",
                ],
            ];
        }

        return [];
    }
}

class TestModel
{
    public $name = "Test";
}


class TestMagicModel
{
    public function __get($name)
    {
        return "Test";
    }
}

class TestNoRulesFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "text",
                "name" => "name",
                "label" => "Name",
            ],
        ];
    }
}
