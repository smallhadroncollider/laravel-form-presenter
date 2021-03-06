<?php

use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;
use SmallHadronCollider\LaravelFormPresenter\FormPresenter;
use SmallHadronCollider\LaravelFormPresenter\FormBuilderProvider;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class FormPresenterTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        Session::start();
        $this->app["request"]->setSession(Session::driver());

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

    public function testFieldRendering()
    {
        $form = new FormPresenter(new TestFieldSet);
        $this->assertEquals('<label for="name">Name</label><input id="name" placeholder="Name" name="name" type="text">', $form->name);
        $this->assertEquals('', $form->invalid);
    }

    public function testPassthrough()
    {
        $form = new FormPresenter(new TestFieldSet);
        $button = $form->submit("Add", ["class" => "button"]);

        $this->assertEquals('<input class="button" type="submit" value="Add">', $button->toHtml());
    }

    public function testOpen()
    {
        $form = new FormPresenter(new TestFieldSet);
        $token = Session::token();

        $this->assertEquals('<form method="POST" action="http://localhost" accept-charset="UTF-8"><input name="_token" type="hidden" value="' . $token . '">', $form->open()->toHtml());
    }

    public function testOpenWithFiles()
    {
        $form = new FormPresenter(new TestFileFieldSet);
        $token = Session::token();

        $this->assertEquals('<form method="POST" action="http://localhost" accept-charset="UTF-8" enctype="multipart/form-data"><input name="_token" type="hidden" value="' . $token . '">', $form->open()->toHtml());
    }
}

class TestFieldSet extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "text",
                "name" => "name",
                "label" => "Name",
            ],
            [
                "type" => "email",
                "name" => "email",
                "label" => "Email",
            ],
        ];
    }
}

class TestFileFieldSet extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "type" => "file",
                "name" => "avatar",
                "label" => "Avatar",
            ],
        ];
    }
}
