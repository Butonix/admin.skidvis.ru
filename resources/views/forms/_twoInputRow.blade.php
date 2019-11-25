<div class="form-group row justify-content-center">
    <label class="col-lg-4 col-form-label text-right" for="input_{{ $type  ?? 'text' }}_{{ (\Illuminate\Support\Str::slug($nameInputFirst)) }}">
        {{ (isset($label)) ? $label : '&emsp;' }}
    </label>
    <div class="col-lg-auto col-md-auto col-sm-auto col-auto mb-lg-0 mb-2 pr-1">
        {{ Form::input($type ?? 'text', $nameInputFirst, old($nameInputFirst) ?? ($valueInputFirst ?? '07:00'), [
			'required' => (isset($requiredFirst) ? true : null),
			'autofocus' => (isset($autofocusFirst) ? true : null),
			'id' => 'input_' . ($type ?? 'text') . '_' . \Illuminate\Support\Str::slug($nameInputFirst),
			'class' => 'form-control ' . ((isset($errors) && $errors->has($nameInputFirst)) ? ' is-invalid ' : '') . ($class ?? ''),
			'placeholder' => ($placeholderFirst ?? null),
			'autocomplete' => ($autocompleteFirst ?? 'on'),
			'data-value' => ($valueInputFirst ?? null),
		] + ($attributes ?? []) + ($attributesFirstFirst ?? []) )
		}}
        @if(isset($feedback) || (isset($errors) && $errors->has($nameInputFirst) === true))
            <div class="invalid-feedback">{{ $feedback ?? $errors->first($nameInputFirst) }}</div>
        @endif

        @isset($textFirst)
            <small class="form-text text-muted"> {!! $textFirst !!}</small>
        @endisset
    </div>
    <div class="col-lg-auto col-md-auto col-sm-auto col-auto mb-lg-0 mb-2 pl-1">
        {{ Form::input($type ?? 'text', $nameInputSecond, old($nameInputSecond) ?? ($valueInputSecond ?? '20:00'), [
			'required' => (isset($requiredSecond) ? true : null),
			'autofocus' => (isset($autofocusSecond) ? true : null),
			'id' => 'input_' . ($type ?? 'text') . '_' . \Illuminate\Support\Str::slug($nameInputSecond),
			'class' => 'form-control ' . ((isset($errors) && $errors->has($nameInputSecond)) ? ' is-invalid ' : '') . ($class ?? ''),
			'placeholder' => ($placeholderSecond ?? null),
			'autocomplete' => ($autocompleteSecond ?? 'on'),
			'data-value' => ($valueInputSecond ?? null),
		] + ($attributes ?? []) + ($attributesSecond ?? []) )
		}}
        @if(isset($feedback) || (isset($errors) && $errors->has($nameInputSecond) === true))
            <div class="invalid-feedback">{{ $feedback ?? $errors->first($nameInputSecond) }}</div>
        @endif

        @isset($textSecond)
            <small class="form-text text-muted"> {!! $textSecond !!}</small>
        @endisset
    </div>
    @if(isset($checkboxActive) && $checkboxActive)
        <div class='col-lg-auto d-flex flex-row justify-content-center align-items-center'>
            {{ Form::checkbox($checkboxName, null, $checkboxChecked ?? true, [
                    'class' => 'form-check-input',
                    'required' => (isset($required) ? 'required' : null),
                    'disabled' => (isset($checkboxDisable)) ? $checkboxDisable : false
			    ])
			}}
        </div>
    @endif
</div>
