<?php

use Illuminate\Support\Collection;
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

        Session::start();
        $this->app["request"]->setSession(Session::driver());
        FieldPresenter::presenter(null);
    }

    public function testBasic()
    {
        $fieldset = new TestFieldSetPresenter();
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" required="true" name="name" type="text">', $fieldset->render());
    }

    public function testAttrs()
    {
        $fieldset = new TestAttrsFieldSetPresenter();
        $this->assertEquals('<label for="name">Name</label><input class="name-field" id="name" placeholder="Name" name="name" type="text">', $fieldset->render());
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

        // Test multi-select
        $fieldset = new TestMultiSelectSetPresenter();
        $test = new PublicisedTestCase();

        $fieldset->populateTest($test);

        $this->assertEquals("name", $test->arguments[1]);
        $this->assertThat($test->arguments[0], $this->isType("array"));
    }

    public function testMultiField()
    {
        TestMultiFieldSetPresenter::clearIndexes();

        foreach (range(0, 2) as $i) {
            $fieldset = new TestMultiFieldSetPresenter();

            $this->assertEquals('<label for="people[' . $i . '][email]">Email</label><input id="people[' . $i . '][email]" placeholder="Email" required="true" name="people[' . $i . '][email]" type="email"><label for="people[' . $i . '][name]">Name</label><input id="people[' . $i . '][name]" placeholder="Name" required="true" name="people[' . $i . '][name]" type="text">', $fieldset->render());
        }
    }

    public function testMultiFieldSetModel()
    {
        $this->markTestSkipped();

        $model = (object) [
            "people" => new Collection([
                (object) ["email" => "one@test.com", "name" => "One"],
                (object) ["email" => "two@test.com", "name" => "Two"],
                (object) ["email" => "three@test.com", "name" => "Three"],
            ]),
        ];

        TestMultiFieldSetPresenter::clearIndexes();

        $fieldset = new TestMultiFieldSetPresenter();
        $fieldset->setModel($model);

        $this->assertCount(6, $fieldset->flatFields());

        $this->assertEquals('<label for="people[0][email]">Email</label><input id="people[0][email]" placeholder="Email" required="true" name="people[0][email]" type="email" value="one@test.com"><label for="people[0][name]">Name</label><input id="people[0][name]" placeholder="Name" required="true" name="people[0][name]" type="text" value="One"><label for="people[1][email]">Email</label><input id="people[1][email]" placeholder="Email" required="true" name="people[1][email]" type="email" value="two@test.com"><label for="people[1][name]">Name</label><input id="people[1][name]" placeholder="Name" required="true" name="people[1][name]" type="text" value="Two"><label for="people[2][email]">Email</label><input id="people[2][email]" placeholder="Email" required="true" name="people[2][email]" type="email" value="three@test.com"><label for="people[2][name]">Name</label><input id="people[2][name]" placeholder="Name" required="true" name="people[2][name]" type="text" value="Three">', $fieldset->render());
    }
}

class PublicisedTestCase extends TestCase
{
    public $arguments;

    public function __call($name, $arguments)
    {
        if ($name === "type" || $name === "select") {
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

class TestAttrsFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "text",
                "name" => "name",
                "label" => "Name",
                "attrs" => ["class" => "name-field"],
            ],
        ];
    }
}

class TestMultiSelectSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "multi-select",
                "name" => "name",
                "label" => "Name",
                "items" => [
                    "1" => "one",
                    "2" => "two",
                    "3" => "three",
                    "4" => "four",
                ]
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

class TestMultiFieldSetPresenter extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "parent" => "people",
                "type" => "email",
                "name" => "email",
                "label" => "Email",
                "rules" => ["required"],
            ],

            [
                "parent" => "people",
                "type" => "text",
                "name" => "name",
                "label" => "Name",
                "rules" => ["required"],
            ],
        ];
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
