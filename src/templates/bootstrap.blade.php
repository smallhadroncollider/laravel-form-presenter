<div class="form-group {{ $errors->has($field->name()) ? "has-error" : "" }}">
    {!! $field->label(["class" => "control-label"]) !!}

    @if ($errors->has($field->name()))
        <span class="help-block">{{ $errors->first($field->name()) }}</span>
    @endif

    {!! $field->display(["class" => "form-control"]) !!}
</div>
