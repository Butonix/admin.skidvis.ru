<?php
/**
 * @var \App\Models\Articles\Article $article
 * @var \App\Models\Files\Image      $image
 */
?>

<div class='row justify-content-center'>
    <div class='col-lg-8'>
        @include('forms._input',[
			'name' => 'name',
			'type' => 'text',
			'label' => 'Название',
			'placeholder' => 'Введите название статьи',
			'attributes' => [
				'readonly' => (isset($readonly)) ? $readonly : false,
				'autofocus' => true
			]
		])
        <div class='row justify-content-between'>
            <div class='col-lg-5'>
                @include('forms._textarea',[
					'name' => 'short_description',
					'type' => 'text',
					'label' => 'Краткое описание',
					'placeholder' => 'Введите краткое описание статьи',
					'attributes' => [
						'readonly' => (isset($readonly)) ? $readonly : false,
						'rows' => 5
					]
				])
            </div>
            <div class='col-lg d-flex flex-row align-items-center justify-content-center'>
                <small class="form-text text-muted">ИЛИ</small>
            </div>
            <div class='col-lg-5'>
                @include('forms._textarea',[
					'name' => 'author',
					'type' => 'text',
					'label' => 'Автор',
					'placeholder' => 'Прим.: Статья от кафе "Печение"',
					'attributes' => [
						'readonly' => (isset($readonly)) ? $readonly : false,
						'rows' => 5
					]
				])
            </div>
        </div>
        @include('forms._checkbox', [
            'name' => 'is_actual',
            'value' => true,
            'label' => 'Актуальная статья',
            'formClass' => 'mb-3',
            'checked' => (isset($article)) ? $article->isActual() : false,
            'attributes' => [
                'disabled' => (isset($checkboxDisable)) ? $checkboxDisable : false
            ]
        ])
        {{--Блок с лейблом--}}
        <div class='row'>
            <div class='col-lg-3 pt-4'>
                Лейбл
            </div>
            <div class='col-lg mt-3'>
                @include('forms._select', [
					'name' => 'article_label_id',
					'list' => ['' => ''] + $labelsList,
					'value' => (isset($article)) ? $article->getArticleLabelId() : null,
					'attributes' => [
						'disabled' => (isset($readonly)) ? $readonly : false
					]
				])
            </div>
        </div>
        {{--Блок с выбором организации--}}
        <div class='row'>
            <div class='col-lg-3 pt-4'>
                Организация
            </div>
            <div class='col-lg mt-3'>
                @include('forms._select', [
					'name' => 'organization_id',
					'list' => ['' => ''] + $organizationsList,
					'value' => (isset($article)) ? $article->getOrganizationId() : null,
					'text' => 'Если статья от лица организации, то выберите организацию из списка',
					'attributes' => [
						'disabled' => (isset($readonly)) ? $readonly : false
					]
				])
            </div>
        </div>
        @if(isset($activeCategoriesSelect) && $activeCategoriesSelect)
            {{--Блок с выбором категорий--}}
            <div class='row'>
                <div class='col-lg-3 pt-4'>
                    Категории
                </div>
                <div class='col-lg mt-3'>
                    @include('forms._select', [
						'class' => 'select--multiple',
						'name' => 'categories[]',
						'list' => $categoriesList,
						'value' => (isset($categoriesId)) ? $categoriesId : null,
						'text' => 'Обязательное поле, до 3х категорий',
						'attributes' => [
							'style' => 'margin-bottom: -2px',
							'multiple' => 'multiple',
							'disabled' => (isset($readonly)) ? $readonly : false
						]
					])
                </div>
            </div>
        @else
            {{--Блок с отображением категорий--}}
            <div class='row mt-3'>
                <div class='col-lg-auto col-auto'>
                    Категории
                </div>
                @forelse($categories as $category)
                    <div class='col-lg-auto col-auto'>
                        <span class='px-3 py-2' style='background-color: #EEEEEE; border-radius: 115px'>
                            {{ $category }}
                        </span>
                    </div>
                @empty
                @endforelse
            </div>
        @endif
        @include('forms._textarea',[
			'name' => 'text',
			'attributes' => [
				'style' => 'display: none',
				'data-editor' => (isset($article)) ? $article->getEditorJsonEncode() : null
			]
		])
        <div id='text-editor' class='text-editor' data-readonly='{{ (isset($readonly)) ? $readonly : false }}'>

        </div>
    </div>
</div>
