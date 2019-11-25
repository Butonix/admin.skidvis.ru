@include('forms._input',[
  'name' => 'l_name',
  'class' => '',
  'label' => 'Фамилия',
])
@include('forms._input',[
    'name' => 'f_name',
    'class' => '',
    'label' => 'Имя',
    'required' => true,
    'text' => 'Обязательное поле',
])
@include('forms._input',[
    'name' => 'm_name',
    'class' => '',
    'label' => 'Отчество',
])
@if(isset($rolesNeed) && $rolesNeed)
    <div class='row justify-content-start'>
        <div class='col-lg-12'>
            @forelse($rolesList as $roleId => $roleDisplayName)
                @include('forms._checkbox',[
					'name' => 'roles[]',
					'label' => $roleDisplayName,
					'value' => $roleId,
					'checked' => false,
					'formClass' => 'mt-2 mb-3 form-check-inline'
				])
            @empty
            @endforelse
        </div>
    </div>
@endif
@include('forms._input',[
    'name' => 'email',
    'class' => '',
    'label' => 'Электронная почта',
    'required' => true,
    'type' => 'email',
    'text' => 'Обязательное поле',
])
@include('forms._input',[
    'name' => 'phone',
    'class' => 'js-mask-phone',
    'label' => 'Телефон',
    'type' => 'tel',
])
@if(isset($createPassword) && $createPassword === true)
    @include('forms._input',[
        'name' => 'password',
        'class' => '',
        'label' => 'Пароль',
        'required' => true,
        'type' => 'password',
    ])

    @include('forms._input',[
        'name' => 'password_confirmation',
        'class' => '',
        'label' => 'Повторите пароль',
        'required' => true,
        'type' => 'password',
    ])
@endif
