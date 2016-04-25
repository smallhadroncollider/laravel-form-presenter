<div class="form-group {{ $errors->has($name) ? "has-error" : "" }}">
    {!! $field->label(["class" => "control-label"]) !!}

    @if ($errors->has($name))
        <span class="help-block">{{ $errors->first($name) }}</span>
    @endif

    {!! $field->display(["class" => "form-control"]) !!}
</div>
