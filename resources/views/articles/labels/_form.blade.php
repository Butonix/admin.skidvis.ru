@include('forms._input',[
    'name' => 'name',
    'type' => 'text',
    'class' => '',
    'label' => 'Название лейбла',
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
