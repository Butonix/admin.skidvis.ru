{!! Form::open(['url' => route('articles.index'), 'method' => 'GET', 'class' => 'form-inline onChangeSubmit']) !!}
{{ Form::search('search', $frd['search'] ?? old('search') ?? null, [
	'data-source' => '/', //route('users.autocomplete')
	'aria-label' => 'Recipients organization',
	'aria-describedby' => 'articles__search',
	'placeholder' => "Поиск...",
	'autocomplete' => 'off',
	'class' => 'form-control js-autocomplete js-on-change-submit grow-1 mr-lg-2 mt-lg-0 mb-lg-2 mr-md-2 mt-md-0 mb-md-2 mr-sm-2 mt-sm-0 mb-sm-2 mr-0 mb-0 mt-3',
]) }}
{!! Form::select('perPage', ['10' => '10', '25' => '25', '50' => '50', '100' => '100', '200' => '200'], $frd['perPage'] ?? old('perPage') ?? null, [
	'placeholder' => 'Кол-во',
	'class' => 'form-control mr-lg-2 mt-lg-0 mb-lg-2 mr-md-2 mt-md-0 mb-md-2 mr-sm-2 mt-sm-0 mb-sm-2 mr-0 mb-0 mt-3'
]) !!}
<div class='col-lg-3 col-md-3 col-12 mt-lg-0 mt-md-0 mt-3 pl-0 pr-lg-2 pr-md-2 pr-sm-2 pr-0' style='margin-top: -8px;'>
    {!! Form::select('categories[]', $categoriesList, $frd['categories'] ?? old('categories') ?? null, [
		'placeholder' => 'Категории',
		'class' => 'select--multiple form-control mr-lg-2 mt-lg-0 mb-lg-2 mr-md-2 mt-md-0 mb-md-2 mr-sm-2 mt-sm-0 mb-sm-2 mr-0 mb-0 mt-3',
		'multiple' => 'multiple',
	]) !!}
</div>
<div class="btn-group mr-lg-2 mt-lg-0 mb-lg-2 mr-md-2 mt-md-0 mb-md-2 mr-sm-2 mt-sm-0 mb-sm-2 mr-0 mb-0 mt-3 ml-lg-1  ml-auto" role="group">
    <a class="btn btn-outline-secondary " href="{{ route('articles.index') }}"
       title="Очистить форму">
        Очистить
    </a>
    <button class="btn btn-primary" type="submit">Найти</button>
</div>
{!! Form::close() !!}
