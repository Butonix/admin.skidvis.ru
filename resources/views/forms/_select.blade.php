<div class="form-group {{ (isset($errors) && $errors->has($name)) ? ' has-danger' : '' }} {{ $formClass ?? null }}">
    @isset($label)
        <label class="form-control-label"
               for="input_{{ $type  ?? 'text' }}_{{ ($name) }}">
            {!! $label !!}&nbsp;
        </label>
    @endisset
    {{ Form::select($name, $list, old($name) ?? ($value ?? null), [
        'class' => 'form-control ' . (isset($errors) && $errors->has(str_replace(['[', ']'], ['.', ''], $name)) ? ' is-invalid ' : '') . ($class ?? ''),
        'required' => (isset($required) ? 'required' : null),
    ] + ($attributes ?? []))
    }}

    @if(isset($feedback) || (isset($errors) && $errors->has(str_replace(['[', ']'], ['.', ''], $name)) === true))
        <div class=" invalid-feedback ">{{ $feedback ?? (isset($errors) && $errors->first(str_replace(['[', ']'], ['.', ''], $name))) }}</div>
    @endif

    @isset($text)
        <small class="form-text text-muted">{{ $text }}.</small>
    @endisset
</div>
