<?php

use Illuminate\Support\Collection;
use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use SmallHadronCollider\LaravelFormPresenter\Fields\Field;
use SmallHadronCollider\LaravelFormPresenter\Fields\FieldInterface;

class FieldPresenterTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        $this->app["request"]->setSession($this->app["session"]->driver("array"));

        $this->presenter = function ($attrs) {
            return "{$attrs["field"]->label()} {$attrs["field"]->display()}";
        };
    }

    public function typesProvider()
    {
        return [
          [[
              "type" => "text",
              "name" => "name",
              "label" => "Name",
          ], '<label for="name">Name</label> <input id="name" placeholder="Name" name="name" type="text">'],

          [[
              "type" => "email",
              "name" => "email",
              "label" => "Email",
          ], '<label for="email">Email</label> <input id="email" placeholder="Email" name="email" type="email">'],

          [[
              "type" => "number",
              "name" => "age",
              "label" => "Age",
          ], '<label for="age">Age</label> <input id="age" placeholder="Age" name="age" type="number">'],

          [[
              "type" => "password",
              "name" => "password",
              "label" => "Password",
          ], '<label for="password">Password</label> <input name="password" type="password" value="" id="password">'],

          [[
              "type" => "date",
              "name" => "date",
              "label" => "Date",
          ], '<label for="date">Date</label> <input id="date" placeholder="Date" name="date" type="date">'],

          [[
              "type" => "datetime",
              "name" => "datetime",
              "label" => "Date & Time",
          ], '<label for="datetime">Date &amp; Time</label> <input id="datetime" placeholder="Date &amp; Time" name="datetime" type="datetime">'],

          [[
              "type" => "file",
              "name" => "file",
              "label" => "File",
          ], '<label for="file">File</label> <input name="file" type="file" id="file">'],

          [[
              "type" => "checkbox",
              "name" => "check",
              "label" => "Check",
              "value" => "1"
          ], '<label for="check">Check</label> <input checked="checked" name="check" type="checkbox" value="1" id="check">'],

          [[
              "type" => "radio",
              "name" => "radio",
              "label" => "Radio",
              "value" => "1"
          ], '<label for="radio">Radio</label> <input checked="checked" name="radio" type="radio" value="1" id="radio">'],

          [[
              "type" => "hidden",
              "name" => "hidden",
              "label" => "Hidden",
          ], ' <input name="hidden" type="hidden">'],

          [[
              "type" => "textarea",
              "name" => "text",
              "label" => "Description",
              "value" => "Blah blah blah"
          ], '<label for="text">Description</label> <textarea id="text" placeholder="Description" name="text" cols="50" rows="10">Blah blah blah</textarea>'],

          [[
              "type" => "select",
              "name" => "type",
              "label" => "Type",
              "items" => [
                  "1" => "Yes",
                  "0" => "No"
              ],
          ], '<label for="type">Type</label> <select id="type" name="type"><option value="1">Yes</option><option value="0">No</option></select>'],

          [[
              "type" => "text",
              "name" => "name",
              "label" => "Name",
              "value" => "name",
          ], '<label for="name">Name</label> <input id="name" placeholder="Name" name="name" type="text" value="name">'],
        ];
    }

    /**
     * @dataProvider typesProvider
     */
    public function testTypes($data, $expected)
    {
        FieldPresenter::presenter($this->presenter);

        $field = new FieldPresenter($data);
        $this->assertEquals($expected, $field->render());
    }

    public function testCustomAttribute()
    {
        FieldPresenter::presenter(function ($attrs) {
            return "{$attrs["field"]->label()} {$attrs["field"]->info()} {$attrs["field"]->display()}";
        });

        $field = new FieldPresenter([
            "name" => "name",
            "label" => "Name",
            "type" => "text",
            "info" => "Some info",
        ]);

        $this->assertEquals('<label for="name">Name</label> Some info <input id="name" placeholder="Name" name="name" type="text">', $field->render());

        $field = new FieldPresenter([
            "name" => "name",
            "label" => "Name",
            "type" => "text",
        ]);

        $this->assertEquals('<label for="name">Name</label>  <input id="name" placeholder="Name" name="name" type="text">', $field->render());
    }

    public function testCheckboxChecked()
    {
        FieldPresenter::presenter($this->presenter);

        $field = new FieldPresenter([
            "type" => "checkbox",
            "name" => "check",
            "label" => "Check",
            "value" => "1",
        ]);

        $field->setModel((object) ["check" => 0]);
        $this->assertEquals('<label for="check">Check</label> <input name="check" type="checkbox" value="1" id="check">', $field->render());

        $field->setModel((object) ["check" => 1]);
        $this->assertEquals('<label for="check">Check</label> <input checked="checked" name="check" type="checkbox" value="1" id="check">', $field->render());
    }

    public function testSelectSetModel()
    {
        FieldPresenter::presenter($this->presenter);

        $field = new FieldPresenter([
            "type" => "select",
            "name" => "select",
            "label" => "Select",
            "items" => [
                "1" => "one",
                "2" => "two",
            ]
        ]);

        // should convert an object to its id
        $field->setModel((object) ["select" => (object) ["id" => 1, "name" => "one"]]);
        $this->assertEquals('<label for="select">Select</label> <select id="select" name="select"><option value="1" selected="selected">one</option><option value="2">two</option></select>', $field->render());

        // should treat a non object as an id
        $field->setModel((object) ["select" => 2]);
        $this->assertEquals('<label for="select">Select</label> <select id="select" name="select"><option value="1">one</option><option value="2" selected="selected">two</option></select>', $field->render());
    }

    public function testMultiSelectSetModel()
    {
        FieldPresenter::presenter($this->presenter);

        $field = new FieldPresenter([
            "type" => "multi-select",
            "name" => "select",
            "label" => "Select",
            "items" => [
                "1" => "one",
                "2" => "two",
                "3" => "three",
            ]
        ]);

        $collection = new Collection([
            (object) ["id" => 1, "name" => "one"],
            (object) ["id" => 2, "name" => "two"],
        ]);

        // should convert a collection to its ids
        $field->setModel((object) ["select" => $collection]);
        $this->assertEquals('<label for="select">Select</label> <select id="select" multiple="true" name="select[]"><option value="1" selected="selected">one</option><option value="2" selected="selected">two</option><option value="3">three</option></select>', $field->render());

        // should treat a non object as an array of ids
        $field->setModel((object) ["select" => [1, 3]]);
        $this->assertEquals('<label for="select">Select</label> <select id="select" multiple="true" name="select[]"><option value="1" selected="selected">one</option><option value="2">two</option><option value="3" selected="selected">three</option></select>', $field->render());
    }

    public function testAttributes()
    {
        FieldPresenter::presenter(function ($attrs) {
            $field = $attrs["field"]->display(["class" => "form__control"]);
            return "{$attrs["field"]->label()} {$field}";
        });

        $field = new FieldPresenter([
            "name" => "name",
            "label" => "Name",
            "type" => "text",
        ]);

        $this->assertEquals('<label for="name">Name</label> <input class="form__control" id="name" placeholder="Name" name="name" type="text">', $field->render());
    }

    public function testCustomFieldType()
    {
        FieldPresenter::presenter($this->presenter);
        FieldPresenter::add("custom", TestCustomFieldType::class);

        $field = new FieldPresenter([
            "name" => "test",
            "label" => "Test",
            "type" => "custom",
        ]);

        $this->assertEquals("label field", $field->render());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Field Type must implement FieldInterface
     **/
    public function testInvalidCustomFieldType()
    {
        FieldPresenter::presenter($this->presenter);
        FieldPresenter::add("custom", TestInvalidFieldType::class);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp /^Invalid type: invalid/
     **/
    public function testInvalidType()
    {
        (new FieldPresenter([
            "type" => "invalid",
            "name" => "name",
            "label" => "Name",
        ]))->render();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage name property missing
     **/
    public function testInvalid()
    {
        (new FieldPresenter(["type" => "text"]))->render();
    }
}

class TestCustomFieldType extends Field implements FieldInterface
{
    public function label($attrs = [])
    {
        return "label";
    }

    public function display($attrs = [])
    {
        return "field";
    }
}

class TestInvalidFieldType {}
