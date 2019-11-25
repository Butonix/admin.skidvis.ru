@include('forms._checkbox',[
	'name' => 'for_products',
	'label' => 'Для акций',
	'value' => true,
	'checked' => (isset($category)) ? $category->forProducts() : false,
	'class' => 'js-categories-types-change',
    'attributes' => [
        'disabled' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._checkbox',[
	'name' => 'for_blog',
	'label' => 'Для блога',
	'value' => true,
	'checked' => (isset($category)) ? $category->forBlog() : false,
    'attributes' => [
        'disabled' => (isset($readonly)) ? $readonly : false
    ]
])
@include('forms._checkbox',[
	'name' => 'is_favorite',
	'label' => 'Избранное',
	'value' => true,
	'checked' => (isset($category)) ? $category->isFavorite() : false,
    'attributes' => [
        'disabled' => (isset($readonly)) ? $readonly : false
    ]
])
<div class='row mt-4'>
    <div class='col-lg-12'>
        @include('forms._input',[
			'name' => 'name',
			'type' => 'text',
			'class' => '',
			'label' => 'Название категории',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false
			]
		])
    </div>
</div>
@include('forms._input',[
  'name' => 'color',
  'type' => 'text',
  'class' => 'js-color-input',
  'label' => 'Цвет иконки',
  'value' => (isset($category) && !empty($category->getColor())) ? $category->getColor() : '#00C2FF',
  'attributes' => [
    'data-index' => 0
  ]
])
<div class="js-color-picker" data-index="0"></div>
