<?php
/**
 * @var \App\Models\Users\User       $user
 * @var \App\Models\Users\Role       $role
 * @var \App\Models\Users\Permission $permission
 */
?>
@extends('layouts.app')

@section('content')
    @include('users.filters._filters', [
        'frd' => $frd,
        'rolesList' => $rolesList,
        'permissionsList' => $permissionsList
    ])
    <div class="container mb-3 px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class='row no-gutters'>
                    <div class="col-lg mb-lg-2 mt-lg-2 col-md mb-md-2 mt-md-2 col-sm mb-sm-2 mt-sm-2 col mb-2 mt-2">
                        <div class='row justify-content-end'>
                            <div class="col-lg-auto col-md-auto col-sm-auto col-auto pr-lg-2 pr-1">
                                <button type='button' class="btn btn-secondary" style='padding-top: .7rem; padding-bottom: .7rem;'
                                        data-tooltip='true' data-placement='bottom' title='Фильтры' id='find-fields-open-button'>
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                            <div class="col-lg-auto col-md-auto col-sm-auto col-auto pl-lg-2 pl-1">
                                <a class="btn btn-success" style='padding-top: .475rem;' href='{{ route('users.create') }}'
                                        data-tooltip='true' data-placement='bottom' title='Новый пользователь'>
                                    <i class="fas fa-user-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-group">
                    @forelse ($users as $user)
                        <div class="list-group-item list-group-item-action">
                            <div class="row" id='user-{{ $user->getKey() }}'>
                                <p title="{{ $user->getEmail() }}" data-original-title='{{ $user->getEmail() }}' class="col-lg mb-lg-0 d-flex align-items-center">
                                    {{ $user->getName() }}
                                </p>
                                <p title="{{ $user->getEmail() }}" data-original-title='{{ $user->getEmail() }}' class="col-lg mb-lg-0 d-flex align-items-center">
                                    <a href="mailto:{{ $user->getEmail() }}">{{ $user->getEmail() }}</a>
                                </p>
                                <p class="col-lg mb-lg-0 d-flex align-items-center">
                                    {{ $user->getPhone() }}
                                </p>
                                <div class="col-lg d-flex align-items-center justify-content-end">
                                    <form action='' style='display: none;'></form>
                                    <div class='btn-group' role='group'>
                                        <a data-tooltip="true" data-placement="bottom" title="Организации"
                                           role='button' class="btn btn-outline-primary" href="{{ route('users.organizations', $user) }}">
                                            <i class="fas fa-landmark"></i>
                                        </a>
                                        <a data-tooltip="true" data-placement="bottom" title="Роли"
                                           role='button' class="btn btn-outline-success" href="{{ route('users.roles', $user) }}">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <a role='button' href="#" class="btn btn-secondary"
                                           data-toggle='collapse' data-target='#collapse-user-{{ $user->getKey() }}' aria-expanded="false"
                                           aria-controls="collapse-user-{{ $user->getKey() }}"
                                           data-tooltip="true" data-placement="bottom" title="Разрешения">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                           href="{{ route('users.edit', $user) }}" role='button' class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a data-tooltip="true" data-placement="bottom" title="Смена пароля"
                                           role='button' class="btn btn-dark" href="{{ route('users.edit.password', $user) }}">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                        {{ Form::open(['url' => route('users.destroy', $user), 'method' => 'delete', 'class' => 'btn btn-danger',
										'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
										'onclick' => 'event.preventDefault(); if (!confirm("Удалить пользователя?")) return; this.submit()']) }}
                                        <i class="far fa-trash-alt"></i>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                            <div class='row no-gutters'>
                                <div id='collapse-user-{{ $user->getKey() }}' class='collapse col-lg-12'
                                     aria-labelledby="user-{{ $user->getKey() }}" data-parent=".list-group-item">
                                    <div class='row no-gutters py-3 mt-3 border-top bg-light'>
                                        @forelse($user->getRolesCollection() as $role)
                                            @forelse($role->permissions as $permission)
                                                <div class='col-lg-4'>
                                                    <i>
                                                        <small>{{ $permission->getDisplayName() }}</small>
                                                    </i>
                                                </div>
                                            @empty
                                                <div class='col-lg'>
                                                    <i>
                                                        <small>Разрешения отсутсвуют</small>
                                                    </i>
                                                </div>
                                            @endforelse
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class='col-lg'>
                            <p>Пользователи отсутствуют</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
		'elements' => $users,
		'frd' => $frd
	])
    @include('users.components.createModal')
@stop
