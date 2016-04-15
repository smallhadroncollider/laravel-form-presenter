<div class="form__group {{ $errors->has($name) ? "form__group--error" : "" }}">
    {!! $field->label() !!}

    @if ($errors->has($name))
        <span class="form__info">{{ $errors->first($name) }}</span>
    @endif

    <div>
        {!! $field->display(["class" => "form__control form__control--grouped form__control--{$type}"]) !!}
    </div>
</div>
