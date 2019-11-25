<div class='row'>
    <div class='col-lg-6'>
        @include('forms._input',[
			'name' => 'inn',
			'type' => 'text',
			'class' => 'js-find-organizations',
			'label' => 'ИНН',
			'placeholder' => 'Введите ваш ИНН',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
			]
		])
        @include('forms._input',[
			'name' => 'orgnip',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'okved',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'address',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'latitude',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'longitude',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'name',
			'type' => 'text',
			'class' => '',
			'label' => 'Название организации',
			'text' => 'Обязательное поле',
			'required' => true,
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'phone',
			'type' => 'text',
			'class' => 'js-mask-phone',
			'label' => 'Телефон',
			'value' => (isset($organization)) ? $organization->getPhone() : null,
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
			]
		])
        @include('forms._input',[
			'name' => 'email',
			'type' => 'text',
			'label' => 'Email',
			'placeholde' => 'Email организации',
			'value' => (isset($organization)) ? $organization->getEmail() : null,
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
    </div>
    <div class='col-lg-6'>
        @include('forms._input',[
			'name' => 'link',
			'type' => 'text',
			'label' => 'Сайт',
			'placeholder' => 'Ссылка на сайт организации',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
			]
		])
        @include('forms._textarea',[
			'name' => 'description',
			'class' => '',
			'label' => 'Описание',
			'placeholder' => 'Почему к вам стоит прийти?',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
				'rows' => 3
			]
		])
        @include('forms._textarea',[
			'name' => 'short_description',
			'class' => '',
			'label' => 'Краткое описание',
			'placeholder' => 'Краткое описание организации',
			'required' => true,
			'text' => 'Обязательное поле',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
				'rows' => 3
			]
		])
        @if(isset($addAvatarShow) && $addAvatarShow)
            @include('forms._file',[
				'name' => 'avatar',
				'class' => '',
				'label' => 'Аватар',
			])
        @endif
        @if(isset($addCoverShow) && $addCoverShow)
            @include('forms._file',[
				'name' => 'cover',
				'class' => '',
				'label' => 'Обложка',
			])
        @endif
    </div>
</div>
<div class='row justify-content-center'>
    <div class='col-lg schedule'>
        <div class='row'>
            <div class='col-lg-12'>
                @forelse($schedule as $day => $dayInfo)
                    @include('forms._twoInputRow', [
						'label' => $dayInfo['nameRus'],
						'type' => 'time',
						'nameInputFirst' => 'operationMode[' . $day . '][start]',
						'nameInputSecond' => 'operationMode[' . $day . '][end]',
						'valueInputFirst' => $dayInfo['start'],
						'valueInputSecond' => $dayInfo['end'],
						'checkboxActive' => true,
						'checkboxName' => 'operationMode[' . $day . '][active]',
						'checkboxChecked' => $dayInfo['active'],
                        'checkboxDisable' => (isset($readonly)) ? $readonly : false,
						'attributes' => [
							'readonly' => (isset($readonly)) ? $readonly : false,
						]
					])
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>
