<div class='row'>
    <div class='col-lg-6'>
        @include('forms._input',[
			'name' => 'name',
			'type' => 'text',
			'class' => '',
			'label' => 'Название акции',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._textarea',[
			'name' => 'description',
			'class' => '',
			'required' => true,
			'label' => 'Описание акции',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
				'rows' => 3
			]
		])
        @include('forms._textarea',[
			'name' => 'conditions',
			'class' => '',
			'label' => 'Условия акции',
			'required' => true,
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
				'rows' => 3
			]
		])
        @include('forms._twoInputRow', [
            'label' => 'Скидка',
            'type' => 'number',
            'nameInputFirst' => 'origin_price',
            'nameInputSecond' => 'value',
            'requiredFirst' => true,
            'requiredSecond' => true,
            'textFirst' => 'Оригинальная цена, руб',
            'textSecond' => 'Размер скидки, %',
            'attributes' => [
                'readonly' => (isset($readonly)) ? $readonly : false
            ]
        ])
        @include('forms._twoInputRow', [
            'label' => 'Время действия',
            'type' => 'date',
            'nameInputFirst' => 'start_at',
            'nameInputSecond' => 'end_at',
            'requiredFirst' => true,
            'textFirst' => 'Обязательное поле',
            'textSecond' => 'Если акция бессрочная, ничего не указывайте в данном поле',
            'attributes' => [
                'readonly' => (isset($readonly)) ? $readonly : false
            ]
        ])
        @include('forms._select', [
			'label' => 'Теги',
			'class' => 'select--multiple',
			'name' => 'tags[]',
			'list' => $tagsList,
			'value' => (isset($product)) ? $chosenTags : null,
			'required' => true,
			'attributes' => [
				'style' => 'margin-bottom: -2px',
				'multiple' => 'multiple',
                'disabled' => (isset($readonly)) ? $readonly : false
			]
		])
        @include('forms._select', [
			'label' => 'Акция по адресам',
			'class' => 'select--multiple',
			'name' => 'points[]',
			'list' => $pointsList,
			'value' => (isset($product)) ? $chosenPoints : null,
			'required' => true,
			'attributes' => [
				'style' => 'margin-bottom: -2px',
				'multiple' => 'multiple',
                'disabled' => (isset($readonly)) ? $readonly : false
			]
		])
    </div>
    <div class='col-lg-6'>
        @include('forms._file',[
			'name' => 'images[]',
			'class' => '',
			'label' => 'Изображения',
			'text' => 'Одновременная загрузка до 7 изображений',
			'attributes' => [
			    'multiple' => 'multiple',
			    'accept' => 'image'
			]
		])
    </div>
</div>
