{!! Form::open(['url' => route('products.all'), 'method' => 'GET', 'class' => 'form-inline onChangeSubmit']) !!}
{{ Form::search('search', $frd['search'] ?? old('search') ?? null, [
	'data-source' => '/', //route('users.autocomplete')
	'aria-label' => 'Recipients organization',
	'aria-describedby' => 'organizations__search',
	'placeholder' => "Поиск...",
	'autocomplete' => 'off',
	'class' => 'form-control js-autocomplete js-on-change-submit grow-1  mb-2 mb-lg-2 mr-2',
]) }}
{!! Form::select('perPage', ['10' => '10', '25' => '25', '50' => '50', '100' => '100', '200' => '200'], $frd['perPage'] ?? old('perPage') ?? null, [
	'placeholder' => 'Кол-во',
	'class' => 'form-control  mb-2 mb-lg-2 mr-2'
]) !!}
{!! Form::select('is_published', [true => 'Опубликовано', false => 'Не опубликовано'], $frd['is_published'] ?? old('is_published') ?? null, [
	'placeholder' => 'Публикация',
	'class' => 'form-control mb-2 mb-lg-2 mr-2'
]) !!}
<div class='col-lg-3 col-md-3 col-12 mt-lg-0 mt-md-0 mt-3 pl-0 pr-lg-2 pr-md-2 pr-sm-2 pr-0 mb-2 mb-lg-0 mb-md-0' style='margin-top: -8px;'>
    {!! Form::select('tags[]', $tagsList, $frd['tags'] ?? old('tags') ?? null, [
		'placeholder' => 'Теги',
		'class' => 'select--multiple form-control mb-2 mb-lg-2 mr-2',
		'multiple' => 'multiple',
	]) !!}
</div>
<div class='col-lg-3 col-md-3 col-12 mt-lg-0 mt-md-0 mt-3 pl-0 pr-lg-2 pr-md-2 pr-sm-2 pr-0 mb-2 mb-lg-0 mb-md-0' style='margin-top: -8px;'>
    {!! Form::select('categories[]', $categoriesList, $frd['categories'] ?? old('categories') ?? null, [
		'placeholder' => 'Категории',
		'class' => 'select--multiple form-control mb-2 mb-lg-2 mr-2',
		'multiple' => 'multiple',
	]) !!}
</div>
<div class="btn-group  mb-2 mb-lg-2 mr-2" role="group">
    <a class="btn btn-outline-secondary " href="{{ route('products.all') }}"
       title="Очистить форму">
        Очистить
    </a>
    <button class="btn btn-primary" type="submit">Найти</button>
</div>
{!! Form::close() !!}
