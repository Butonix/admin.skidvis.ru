@include('forms._input',[
    'name' => 'name',
    'class' => '',
    'required' => true,
    'label' => 'Название сервиса',
])
@include('forms._input',[
	'name' => 'slug',
	'class' => '',
	'required' => $isEditableSlug,
	'label' => 'Ярлык сервиса',
	'text' => 'Например, сервис - Google, ярлык - google. Все в нижнем регистре, без пробелов, дефисов и подчеркиваний.',
	'attributes' => [
	    'readonly' => !$isEditableSlug
	]
])
@include('forms._input',[
	'name' => 'icon_url',
	'class' => '',
	'label' => 'URL иконки',
])
@include('forms._input',[
	'name' => 'client_id',
	'class' => '',
	'required' => true,
	'label' => 'Идентификатор клиента',
	'value' => (isset($clientId)) ? $clientId : null,
    'autocomplete' => 'off'
])
@include('forms._input',[
	'name' => 'client_secret',
	'class' => '',
	'required' => true,
	'label' => 'Секретный ключ',
	'value' => (isset($clientSecret)) ? $clientSecret : null,
    'autocomplete' => 'off'
])
