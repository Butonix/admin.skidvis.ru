<div class='row justify-content-center'>
    <div class='col-lg-6'>
        @include('forms._input',[
			'name' => 'name',
			'type' => 'text',
			'class' => '',
			'label' => 'Название точки',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'phone',
			'type' => 'text',
			'class' => 'js-mask-phone',
			'label' => 'Телефон',
			'text' => 'Номер телефона без кода страны',
			'value' => (isset($point)) ? $point->getPhone() : null,
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
				'maxlength' => 10
			]
		])
        @include('forms._input',[
			'name' => 'email',
			'type' => 'text',
			'class' => '',
			'label' => 'Email',
			'value' => (isset($point)) ? $point->getEmail() : null,
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'full_street',
			'type' => 'text',
			'class' => 'js-find-addresses',
			'label' => 'Адрес',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'street',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._input',[
			'name' => 'building',
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
			'name' => 'latitude',
			'type' => 'hidden',
			'class' => '',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
    </div>
</div>
