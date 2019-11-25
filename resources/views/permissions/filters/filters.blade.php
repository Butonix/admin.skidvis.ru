{!! Form::open(['url' => route('permissions.index'), 'method' => 'GET', 'class' => 'form-inline onChangeSubmit']) !!}
{{ Form::search('search', $frd['search'] ?? old('search') ?? null,[
	'data-source' => '/', //route('permissions.autocomplete')
	'aria-label' => 'Recipients permissionname',
	'aria-describedby' => 'permissions__search',
	'placeholder' => "Поиск...",
	'autocomplete' => 'off',
	'class' => 'form-control grow-1 js-autocomplete js-on-change-submit mr-2 mb-3',
]) }}
{!! Form::select('perPage', ['10' => '10', '25' => '25', '50' => '50', '100' => '100', '200' => '200'], $frd['perPage'] ?? old('perPage') ?? null, [
	'placeholder' => 'Кол-во',
	'class' => 'form-control mr-2 mb-3'
])
!!}
<div class="btn-group mb-3" role="group">
    <a class="btn btn-outline-secondary " href="{{ route('permissions.index') }}"
       title="Очистить форму">Очистить
    </a>
    <button class="btn btn-primary" type="submit">Вперед!</button>
</div>
{!! Form::close() !!}
