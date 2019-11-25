<div class='row mt-3'>
  @forelse ($categories as $category)
    <div class='col-lg-3 col-md-4 col-sm-4 col col-pr'>
      <div>
        <div class='category-card'>
          <div class='category-card__wrapper'>
            <div class='category-card__img'>
              <div class='category-card__img__content bg-cover lazyload' data-src='{{ $category->getImageLink() }}'>
              </div>
            </div>
            <div class='category-card__body'>
              <div class='category-card__body__name'>{{ $category->getName() }}</div>
              @if(Auth::user()->canUpdateCategories())
                <div class='category-card__body__data'>
                  <div>
                    <label class="col-lg form-check-label d-flex align-items-center">
                      {{ Form::open(['url' => route('categories.ordering', $category), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}
                      @include('forms._input',[
                        'name' => 'ordering',
                        'type' => 'number',
                        'label' => 'Сортировка',
                        'labelClass' => 'mb-0',
                        'value' => $category->getOrdering(),
                        'attributes' => [
                          'min'=>0,
                          'max'=>9999999,
                          'style'=>'max-width: 100px'
                        ],
                      ])
                      {{ Form::close() }}
                    </label>
                  </div>
                  <div>
                    <label class="col-lg form-check-label d-flex align-items-center">
                      {{ Form::open(['url' => route('categories.favorite', $category), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}
                      @include('forms._checkbox',[
'name' => 'is_favorite',
'label' => 'Избранное',
'checked' => $category->isFavorite(),
])
                      {{ Form::close() }}
                    </label>
                  </div>
                  <div>
                    <label class="col-lg form-check-label d-flex align-items-center">
                      {{ Form::open(['url' => route('categories.for-products', $category), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}
                      @include('forms._checkbox',[
'name' => 'for_products',
'label' => 'Для акций',
'checked' => $category->forProducts(),
])
                      {{ Form::close() }}
                    </label>
                  </div>
                  <div>
                    <label class="col-lg form-check-label d-flex align-items-center">
                      {{ Form::open(['url' => route('categories.for-blog', $category), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}
                      @include('forms._checkbox',[
'name' => 'for_blog',
'label' => 'Для блога',
'checked' => $category->forBlog(),
])
                      {{ Form::close() }}
                    </label>
                  </div>
                </div>
              @endif
              <div class='category-card__body__buttons'>
                <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                   href="{{ route('categories.show', $category) }}" role='button' class="btn btn-outline-info">
                  <i class="fas fa-eye"></i>
                </a>
                @if(Auth::user()->canUpdateCategories())
                  <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                     href="{{ route('categories.edit', $category) }}" role='button' class="btn btn-outline-primary">
                    <i class="fas fa-edit"></i>
                  </a>
                @endif
                @if(Auth::user()->canDeleteCategories())
                  {{ Form::open(['url' => route('categories.destroy', $category), 'method' => 'delete', 'class' => 'btn btn-danger',
'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
'onclick' => 'event.preventDefault(); if (!confirm("Удалить категорию?")) return; this.submit()']) }}
                  <i class="far fa-trash-alt"></i>
                  {{ Form::close() }}
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class='col-lg-12'>
      <p>Категории отсутствуют</p>
    </div>
  @endforelse
</div>
