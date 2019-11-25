@include('forms._checkbox',[
	'name' => 'is_favorite',
	'label' => 'Избранное',
	'value' => true,
	'checked' => (isset($holiday)) ? $holiday->isFavorite() : false,
    'attributes' => [
        'disabled' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._input',[
    'name' => 'name',
    'type' => 'text',
    'class' => '',
    'label' => 'Название праздника/выходного',
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
