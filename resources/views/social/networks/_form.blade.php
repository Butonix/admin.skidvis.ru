@include('forms._input',[
    'name' => 'name',
    'type' => 'text',
    'class' => '',
    'text' => 'Обязательное, уникальное название',
    'label' => 'Псеводним',
    'required' => true,
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._input',[
    'name' => 'display_name',
    'type' => 'text',
    'class' => '',
    'label' => 'Название соц.сети',
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._input',[
    'name' => 'link',
    'type' => 'text',
    'class' => '',
    'label' => 'Ссылка на соц.сеть',
    'text' => 'Обязательное поле',
    'required' => true,
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._input',[
    'name' => 'icon_url',
    'type' => 'text',
    'class' => '',
    'label' => 'URL иконки',
    'attributes' => [
        'readonly' => (isset($readonly)) ? $readonly : false
    ]
])

