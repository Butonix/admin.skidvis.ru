@include('forms._checkbox',[
	'name' => 'is_favorite',
	'label' => 'Избранное',
	'value' => true,
	'checked' => (isset($auditory)) ? $auditory->isFavorite() : false,
    'attributes' => [
        'disabled' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._input',[
    'name' => 'name',
    'type' => 'text',
    'class' => '',
    'label' => 'Название аудитории',
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
