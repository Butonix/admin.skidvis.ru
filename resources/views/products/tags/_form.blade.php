@include('forms._input',[
    'name' => 'name',
    'type' => 'text',
    'class' => '',
    'label' => 'Название тега',
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
