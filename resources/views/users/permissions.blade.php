<?php
/**
 * @var \App\Models\Users\User $user
 * @var \App\Models\Users\Permission $permission
 * @var \App\Models\Users\Permission $userPermission
 * @var \App\Models\Users\Permission $permissionsOfRole
 */
?>
@extends('layouts.app')

@section('content')
    @include('users.filters._filtersPermissions', [
        'frd' => $frd,
        'user' => $user
    ])
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                {{ Form::open(['url' => route('users.permissions.update', $user), 'method' => 'PATCH', 'class'  =>  '']) }}
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
                                data-toggle="collapse" data-target="#userPermissions" aria-expanded="false" aria-controls="userPermissions">
                            Разрешения пользователя <span class="badge badge-secondary">{{ $permissionsCount }}</span>
                        </button>
                    </div>
                </div>

                <div class="collapse" id="userPermissions">
                    <div class="list-group mb-3">
                        {{-- Для начала выводятся все разрешения от ролей пользователя, которые нельзя убрать --}}
                        @forelse ($userPermissionsOfRoles as $permissionsOfRole)
                            <div class="list-group-item list-group-item-action">
                                <div class="row">
                                    <label class="col-lg form-check-label">
                                        @include('forms._checkbox',[
											'name'  =>  '',
											'label'  =>  $permissionsOfRole->getDisplayName(),
											'checked' => true,
											'attributes' => [
											    'disabled' => true
											]
										])
                                    </label>
                                    <div class="col-lg">
                                        <small class="text-muted">
                                            <i>
                                                {{ $permissionsOfRole->getName() }}
                                            </i>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                        {{-- Затем выводятся разрешения, которые были назначены пользователю отдельно от ролей, можно изменить --}}
                        @forelse ($userPermissions as $userPermission)
                            <div class="list-group-item list-group-item-action">
                                {{ Form::hidden('permissions[off][' . $userPermission->getKey().']') }}
                                <div class="row">
                                    <label class="col-lg form-check-label">
                                        @include('forms._checkbox',[
											'name'  =>  'permissions[on][' . $userPermission->getKey() .']',
											'label'  =>  $userPermission->getDisplayName(),
											'checked' => true
										])
                                    </label>
                                    <div class="col-lg">
                                        <small class="text-muted">
                                            <i>
                                                {{ $userPermission->getName() }}
                                            </i>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item list-group-item-action">
                                <div class='col-lg'>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="list-group">
                    @forelse ($permissions as $permission)
                        <div class="list-group-item list-group-item-action">
                            {{ Form::hidden('permissions[off][' . $permission->getKey().']') }}
                            <div class="row">
                                <label class="col-lg form-check-label">
                                    @include('forms._checkbox',[
                                        'name'  =>  'permissions[on][' . $permission->getKey() .']',
                                        'label'  =>  $permission->getDisplayName(),
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
                        <div class="list-group-item list-group-item-action">
                            <div class='col-lg'>
                                <p>Разрешения не найдены</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('forms._pagination', [
		'elements' => $permissions,
		'frd' => $frd
	])
@stop
