<?php
/**
 * @var \App\Models\Users\Role $role
 * @var \App\Models\Users\Permission $permission
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pt-lg-3">
        <div class="row">
            <div class="col-lg-auto">
                {!! Form::open(['url' => route('roles.permissions', $role), 'method' => 'GET', 'class' =>  'form-inline']) !!}
                {{ Form::search('search', $frd['search'] ?? old('search') ?? null, [
                    'data-source'  =>  '/', //route('roles.autocomplete')
                    'aria-label'  =>  'Recipients permissionname',
                    'aria-describedby'  =>  'permissions__search',
                    'placeholder'  =>  "Поиск...",
                    'class'  =>  'form-control grow-1 js-autocomplete js-on-change-submit mr-lg-2 mb-lg-2',
                ]) }}
                {!! Form::select('perPage', ['10'  =>  '10', '25'  =>  '25', '50'  =>  '50', '100'  =>  '100', '200'  =>  '200'], $frd['perPage'] ?? old('perPage') ?? null, [
                    'placeholder'  =>  'Кол-во',
                    'class'  =>  'form-control mr-lg-2 mb-lg-2'
                ])
                !!}
                <div class="btn-group mr-lg-2 mb-lg-2" role="group">
                    <a class="btn btn-outline-secondary" href="{{ route('roles.permissions', $role) }}"
                       title="Очистить форму">Очистить
                    </a>
                </div>
                <button class="btn btn-primary mb-lg-2" type="submit">Вперед!</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="container pt-lg-3 pb-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                {{ Form::model($role, ['url' => route('roles.permissions.update', $role), 'method' => 'PATCH', 'class' => 'onSubmitAjax']) }}
                <div class="btn-group mb-lg-2 mt-lg-2">
                    <a href='#'
                       class='btn btn-outline-secondary check-all'>
                        @include('forms._checkbox',[
							'name'  =>  ' ',
							'checked'  =>  false,
						])
                    </a>
                    <button type="submit" class="btn btn-success" onclick="return confirm('Сохранить?')">
                        Сохранить
                    </button>
                </div>
                <div class="list-group">
                    @forelse ($permissions as $permission)
                        <div class="list-group-item list-group-item-action">
                            {{ Form::hidden('permissions[off][' . $permission->getKey() . ']') }}
                            <div class="row">
                                <label class="col-lg form-check-label">
                                    @include('forms._checkbox',[
                                        'name'  =>  'permissions[on][' . $permission->getKey() . ']',
                                        'label'  =>  $permission->getDisplayName(),
                                        'checked'  =>  $role->hasPermission([$permission->getName()]),
                                    ])
                                </label>
                                <div class="col-lg">
                                    <small class="text-muted">
                                        <i>
                                            {{ $permission->getName() }}
                                        </i>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No permissions</p>
                    @endforelse
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    {{ $permissions->links() }}
@stop
