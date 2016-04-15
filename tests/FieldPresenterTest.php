<?php

use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;

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

    public function testToString()
    {
        FieldPresenter::presenter($this->presenter);

        $field = new FieldPresenter([
            "name" => "name",
            "label" => "Name",
            "type" => "text",
        ]);

        $this->assertEquals('<label for="name">Name</label> <input id="name" placeholder="Name" name="name" type="text">', $field);
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

        $field->setData(["check" => 0]);
        $this->assertEquals('<label for="check">Check</label> <input name="check" type="checkbox" value="1" id="check">', $field->render());

        $field->setData(["check" => 1]);
        $this->assertEquals('<label for="check">Check</label> <input checked="checked" name="check" type="checkbox" value="1" id="check">', $field->render());
    }

    public function testBoolean()
    {
        FieldPresenter::presenter($this->presenter);

        $field = new FieldPresenter([
            "name" => "testing",
            "label" => "Testing",
            "type" => "boolean",
        ]);

        $this->assertEquals('<label for="testing">Testing</label> <input id="testing_yes" checked="checked" name="testing" type="radio" value="1"> <label for="testing_yes">Yes</label> <input id="testing_no" name="testing" type="radio" value="0"> <label for="testing_no">No</label>', $field->render());

        $field->setData(["testing" => false]);

        $this->assertEquals('<label for="testing">Testing</label> <input id="testing_yes" name="testing" type="radio" value="1"> <label for="testing_yes">Yes</label> <input id="testing_no" checked="checked" name="testing" type="radio" value="0"> <label for="testing_no">No</label>', $field->render());
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

        $this->assertEquals('<label for="name">Name</label> <input id="name" placeholder="Name" class="form__control" name="name" type="text">', $field->render());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp /^Invalid type: invalid \(allowed types: [a-z,\s]+\)$/
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
