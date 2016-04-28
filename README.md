# Laravel Form Presenter

A Laravel package for easily generating and manipulating forms.

Dealing with forms in plain HTML leads to a horrible mess. Using the [Laravel Collective HTML & Forms](https://laravelcollective.com/docs/5.2/html) package makes things neater, but the logic for your form is still all over the place. Using Laravel Form Presenter you can define your form in one place and display it using a reusable `form` template.

## Features

- Easy to add your own field types
- Adds CSRF field automatically
- Adds required attributes on required fields
- (Almost) Automatic integration testing
- Automatically detects `file` field types and adds the necessary form attributes

## Example

Add `SmallHadronCollider\LaravelFormPresenter\Providers\FormPresenterServiceProvider::class` to your service providers in `config/app.php`.

```php
<?php

// app/Http/Presenters/PersonFieldSet.php

namespace App\Http\Presenters;

use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;

class PersonFieldSet extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "name" => "first_name",
                "type" => "text",
                "label" => "First Name",
            ],
            [
                "name" => "last_name",
                "type" => "text",
                "label" => "Last Name",
                "rules" => ["required"],
            ],
            [
                "name" => "age",
                "type" => "number",
                "label" => "Age",
                "rules" => ["required"],
            ],
            [
                "name" => "notes",
                "type" => "textarea",
                "label" => "Notes",
            ],
        ];
    }
}
```

```php
<?php

// app/Http/Controllers/PersonController.php

namespace App\Http\Controllers;

use App\Http\Presenters\PersonFieldSet;
use App\Http\Requests\PersonFormRequest;
use App\Repositories\PersonRepository;

class PersonController extends Controller
{
    private $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(PersonFieldSet $fieldSet, $id)
    {
        $person = $this->repository->find($id);

        // Set the form data from the $person entity
        $fieldSet->setModel($person);

        return view("form", [
            // Pass in the form for the fieldset
            "form" => $fieldSet->form(),
            "title" => "Update Person"
        ]);
    }

    public function post(PersonFieldSet $fieldSet, PersonFormRequest $request, $id)
    {
        $person = $this->repository->find($id);

        // Get the data for each form field
        $data = $request->only($fieldSet->fieldNames());
        $this->repository->update($person, $data);

        return redirect("/people/{$person->id}");
    }
}
```

Get the validation rules from the fieldset:

```php
<?php

// app/Http/Request/PersonFormRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Presenters\FieldSets\PersonFieldSet;

class PersonFormRequest extends FormRequest
{
    public function rules(PersonFieldSet $fieldset)
    {
        return $fieldset->rules();
    }
}
```

You can then use a single `form.blade.php` file to render all your forms:

```php
{{-- form.blade.php --}}

{!! $form->open(["class" => "form"]) !!}
    {!! $form->display() !!}
    {!! $form->submit($title, ["class" => "button button--blue"]) !!}
{!! $form->close() !!}
```

Or, if you want more flexibility, you can render individual fields in fieldset specific templates:

```php
{{-- person-form.blade.php --}}

{!! $form->open(["class" => "form"]) !!}
    {!! $form->first_name !!}
    {!! $form->last_name !!}
    {!! $form->submit($title, ["class" => "button button--blue"]) !!}
{!! $form->close() !!}
```

### Using With Analogue

If you're using [Analogue ORM](https://github.com/analogueorm/analogue) you'll need to use a different Model Presenter (Model Presenters tell the package how to set field values from a model):

```php
<?php

// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use SmallHadronCollider\LaravelFormPresenter\ModelPresenterInterface;
use SmallHadronCollider\LaravelFormPresenter\ModelPresenters\AnalogueModelPresenter;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ModelPresenterInterface::class, AnalogueModelPresenter::class);
    }
}
```



### Custom Field Rendering

You can use render a field however you like:

```php
{{-- forms/field.blade.php --}}

<div class="form__group {{ $errors->has($field->name()) ? "form__group--error" : "" }}">
    {!! $field->label(["class" => "form__label"]) !!}

    @if ($errors->has($field->name()))
        <span class="form__info">{{ $errors->first($field->name()) }}</span>
    @endif

    {!! $field->display(["class" => "form__control form__control--grouped form__control--{$field->type()}"]) !!}
</div>
```

```php
<?php

// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        FieldPresenter::presenter(function ($attrs) {
            return view("forms/field", $attrs);
        });
    }
}
```

### Custom Field Properties

```php
<?php

// app/Http/Presenters/PersonFieldSet.php

namespace App\Http\Presenters;

use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;

class PersonFieldSet extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            [
                "name" => "first_name",
                "type" => "text",
                "label" => "First Name",

                // Custom field property
                "message" => "Enter you first name"
            ],
        ];
    }
}
```

Reserved property names: `name`, `type`, `label`, `value`, `rules`, `items`

Using a custom field renderer as above:

```php
{{-- forms/field.blade.php --}}

<div class="form__group {{ $errors->has($field->name()) ? "form__group--error" : "" }}">
    {!! $field->label(["class" => "form__label"]) !!}

    @if ($errors->has($field->name()))
        <span class="form__info">{{ $errors->first($field->name()) }}</span>
    @endif

    {{-- use custom message field - will return null if no value set --}}
    <p class="form__message">{!! $field->message() !!}</p>

    {!! $field->display(["class" => "form__control form__control--grouped form__control--{$field->type()}"]) !!}
</div>
```



### Bootstrap Field Rendering

You can use the built-in Bootstrap template:

```php
<?php

// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        FieldPresenter::presenter(function ($attrs) {
            return view("smallhadroncollider/laravel-form-presenter::bootstrap", $attrs);
        });
    }
}
```

### Adding A Custom Field Type

You can easily add your own custom field type by extending the Field class:

```php
<?php

// app/Http/Presenters/Forms/Fields/Boolean.php

namespace App\Http\Presenters\Forms\Fields;

use Illuminate\Foundation\Testing\TestCase;
use Faker\Generator;

use SmallHadronCollider\LaravelFormPresenter\Fields\AbstractField;
use SmallHadronCollider\LaravelFormPresenter\Fields\FieldInterface;

class Boolean extends AbstractField implements FieldInterface
{
    // The html to display for the label
    public function label($attrs = [])
    {
        return $this->formBuilder->label($this->name, $this->label, $attrs);
    }

    // The html to display for the field
    public function display($attrs = [])
    {
        return view("embellish::forms/boolean", [
            "name" => $this->name,
            "value" => $this->value() || $this->value() === null,
        ]);
    }

    // Test function for integration testing
    public function test(TestCase $test, Generator $faker)
    {
        return mt_rand(0, 1) ? $test->check($this->name) : $test->uncheck($this->name);
    }
}
```

```php
{{-- forms/boolean.blade.php --}}

<div>
    <label>
      <input type="radio" value="1" name="{{ $name }}"{{ $value ? " checked" : "" }}> Yes
    </label>
    <label>
      <input type="radio" value="0" name="{{ $name }}"{{ $value ? "" : " checked" }}> No
    </label>
</div>
```

```php
<?php

// app/Providers/AppServiceProvider

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use SmallHadronCollider\LaravelFormPresenter\FieldPresenter;
use App\Http\Presenters\Forms\Fields\Boolean;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Add boolean field type
        FieldPresenter::add("boolean", Boolean::class);
    }
}
```

## Integration Testing

You can setup integration tests for any fieldset automatically:

```php
<?php

// tests\AddPersonTest.php

use TestCase;
use SmallHadronCollider\LaravelFormPresenter\PubliciseFormMethods;
use App\Http\Presenters\FieldSets\PersonFieldSet;

class AddPersonTest extends TestCase
{
    // puts form filling methods (type(), select(), etc.) into public scope using __call()
    use PubliciseFormMethods;

    public function testPersonForm()
    {
        // create fieldset
        $fieldset = new PersonFieldSet();

        // visit page
        $test = $this->visit("/admin/people/create");

        // populate test form automatically, then submit
        $fieldset->populateTest($test)->press("Create Person")->assertResponseOk();
    }
}
```

## To Do

- Better documentation (nested fieldset)
- Add some example files
- Multiple fieldsets per form
- Custom fieldset rendering


## License

The MIT License (MIT)
Copyright &copy; 2016 Small Hadron Collider / Mark Wales

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
