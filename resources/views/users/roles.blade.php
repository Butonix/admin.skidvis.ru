<?php
/**
 * @var \App\Models\Users\Role $role
 * @var \App\Models\Users\Role $userRole
 */
?>
@extends('layouts.app')

@section('content')
    @include('users.filters._filtersRoles', [
        'frd' => $frd,
        'user' => $user
    ])
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                {{ Form::open(['url' => route('users.roles.update', $user), 'method' => 'PATCH', 'class'  =>  '']) }}
                <div class='row mb-3'>
                    <div class='col-lg'>
                        <div class="btn-group">
                            <a href='#'
                               class='btn btn-outline-secondary check-all'>
                                @include('forms._checkbox',[
									'name'  =>  ' ',
									'checked'  =>  false,
								])
                            </a>
                            <button type="submit" class="btn btn-success">
                                Сохранить
                            </button>
                        </div>

                        <button class="btn btn-primary ml-3" type="button" style='padding: 0.5rem 0.7rem .4rem;'
                                data-toggle="collapse" data-target="#userRoles" aria-expanded="false" aria-controls="userRoles">
                            Роли пользователя <span class="badge badge-secondary">{{ $rolesCount }}</span>
                        </button>
                    </div>
                </div>

                <div class="collapse" id="userRoles">
                    <div class="list-group mb-3">
                        @forelse ($userRoles as $userRole)
                            <div class="list-group-item list-group-item-action">
                                {{ Form::hidden('roles[off][' . $userRole->getKey().']') }}
                                <div class="row">
                                    <label class="col-lg form-check-label">
                                        @include('forms._checkbox',[
											'name'  =>  'roles[on][' . $userRole->getKey() .']',
											'label'  =>  $userRole->getDisplayName(),
											'checked' => true
										])
                                    </label>
                                    <div class="col-lg">
                                        <small class="text-muted">
                                            <i>
                                                {{ $userRole->getName() }}
                                            </i>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item list-group-item-action">
                                <div class='col-lg'>
                                    <p>Пользователь не имеет ролей</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="list-group">
                    @forelse ($roles as $role)
                        <div class="list-group-item list-group-item-action">
                            {{ Form::hidden('roles[off]['.$role->name.']') }}
                            <div class="row">
                                <label class="col-lg form-check-label">
                                    @include('forms._checkbox',[
                                        'name'  =>  'roles[on]['.$role->name .']',
                                        'label'  =>  $role->getDisplayName(),
                                        'checked'  =>  $user->hasRole([$role->getName()]),
                                    ])
                                </label>
                                <div class="col-lg">
                                    <small class="text-muted">
                                        <i>
                                            {{ $role->getName() }}
                                        </i>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No roles</p>
                    @endforelse
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('forms._pagination', [
		'elements' => $roles,
		'frd' => $frd
	])
@stop
