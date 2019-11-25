<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 * @var \App\Models\Organizations\Organization $userOrganization
 */
?>
@extends('layouts.app')

@section('content')
    @include('users.filters._filtersOrganizations', [
        'frd' => $frd,
        'user' => $user
    ])
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                {{ Form::open(['url' => route('users.organizations.update', $user), 'method' => 'PATCH', 'class'  =>  '']) }}
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
                                data-toggle="collapse" data-target="#userOrganizations" aria-expanded="false" aria-controls="userOrganizations">
                            Организации пользователя <span class="badge badge-secondary">{{ $userOrganizationsCount }}</span>
                        </button>
                    </div>
                </div>

                <div class="collapse" id="userOrganizations">
                    <div class="list-group mb-3">
                        @forelse ($userOrganizations as $userOrganization)
                            <div class="list-group-item list-group-item-action">
                                {{ Form::hidden('organizations[off][' . $userOrganization->getKey().']') }}
                                <div class="row">
                                    <label class="col-lg form-check-label">
                                        @include('forms._checkbox',[
											'name'  =>  'organizations[on][' . $userOrganization->getKey() .']',
											'label'  =>  $userOrganization->getName(),
											'checked' => true
										])
                                    </label>
                                    <div class="col-lg">
                                        {{ $userOrganization->getPhone() }}
                                    </div>
                                    <div class="col-lg">
                                        {{ $userOrganization->getEmail() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item list-group-item-action">
                                <div class='col-lg'>
                                    <p>Пользователь не привязан к организациям</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="list-group">
                    @forelse ($organizations as $organization)
                        <div class="list-group-item list-group-item-action">
                            {{ Form::hidden('organizations[off][' . $organization->getKey().']') }}
                            <div class="row">
                                <label class="col-lg form-check-label">
                                    @include('forms._checkbox',[
                                        'name'  =>  'organizations[on][' . $organization->getKey() .']',
                                        'label'  =>  $organization->getName(),
                                    ])
                                </label>
                                <div class="col-lg">
                                    {{ $organization->getPhone() }}
                                </div>
                                <div class="col-lg">
                                    {{ $organization->getEmail() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>Организации не найдены</p>
                    @endforelse
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('forms._pagination', [
		'elements' => $organizations,
		'frd' => $frd
	])
@stop
