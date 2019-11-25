<div class=" mb-3 {{ (isset($errors) && $errors->has($name)) ? ' has-danger' : '' }}">
    <span>{{ $label ?? 'Выберите файл' }}</span>
    <div class="input-group mt-2">
        <div class="custom-file">
            {{ Form::file($name, [
                'required' => (isset($required) ? 'required' : null),
                'id' => 'input_file_' . \Illuminate\Support\Str::slug($name),
                'class' => ' js-custom-file-input custom-file-input' . ((isset($errors) && $errors->has($name))? ' is-invalid ' : '') . ($class ?? ''),
                'placeholder' => ($placeholder ?? null),
                'autocomplete' => ($autocomplete ?? 'on'),
                'data-value' => ($value ?? null),
            ] + ($attributes ?? []))
            }}
            <label class="custom-file-label"
                   for="{{ 'input_file_' . \Illuminate\Support\Str::slug($name) }}">
                Выберите файл
            </label>
        </div>
    </div>

    @if(isset($feedback) || (isset($errors) && $errors->has($name) === true))
        <div class="invalid-feedback">{{ $feedback ?? $errors->first($name) }}</div>
    @endif

    @isset($text)
        <small class="form-text text-muted"> {!! $text !!}</small>
    @endisset
</div>
