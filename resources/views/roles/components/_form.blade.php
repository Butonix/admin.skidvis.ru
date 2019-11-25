@include('forms._input',[
    'name' => 'name',
    'class' => '',
    'required' => true,
    'text' => 'Обязательное, уникальное поле',
    'label' => 'Псевдоним',
])
@include('forms._input',[
    'name' => 'display_name',
    'class' => '',
    'label' => 'Отображаемое название',
])
@include('forms._textarea',[
    'name' => 'description',
    'class' => '',
    'label' => 'Описание роли',
])
