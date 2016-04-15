# Laravel Form Presenter

A Laravel package for easily generating and manipulating forms

## Example

```php
<?php

// PersonFieldSet.php

namespace App\Http\Presenters;

use SmallHadronCollider\LaravelFormPresenter\FieldSetPresenter;

class PersonFieldSet extends FieldSetPresenter
{
    protected function fields()
    {
        return [
            $this->field([
                "name" => "first_name",
                "type" => "text",
                "label" => "First Name",
            ]),

            $this->field([
                "name" => "last_name",
                "type" => "text",
                "label" => "Last Name",
            ]),

            $this->field([
                "name" => "age",
                "type" => "number",
                "label" => "Age",
            ]),

            $this->field([
                "name" => "notes",
                "type" => "textarea",
                "label" => "Notes",
            ]),
        ];
    }
}
```

```php
<?php

// PersonController.php

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
        $fieldSet->setModel($person)->exclude(["notes"]);

        return view("form", [
            "form" => $fieldSet->form(),
        ]);
    }

    public function post(PersonFieldSet $fieldSet, PersonFormRequest $request, $id)
    {
        $person = $this->repository->find($id);
        $this->repository->update($person, $request->only($fieldSet->fieldNames()));

        return redirect("/people/{$person->id}");
    }
}
```

```php
{{-- form.blade.php --}}

{!! $form->open(["class" => "form"]) !!}
    {!! $form !!}
    {!! $form->submit("Submit", ["class" => "button button--blue"]) !!}
{!! $form->close() !!}
```
