<?php
/**
 * @var \App\Models\Organizations\Point        $point
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    @include('organizations.points.filters._filters', [
        'organization' => $organization,
        'frd' => $frd
    ])
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class='row no-gutters justify-content-end'>
                    <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2 mr-2">
                        <button type='button' class="btn btn-secondary" style='padding-top: .7rem; padding-bottom: .7rem;'
                                data-tooltip='true' data-placement='bottom' title='Фильтры' id='find-fields-open-button'>
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    @if(Auth::user()->canCreatePoints())
                        <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2">
                            <a href='{{ route('points.create', $organization) }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                               data-tooltip='true' data-placement='bottom' title='Новая статья'>
                                Добавить <i class="far fa-newspaper"></i>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="list-group">
                    @forelse ($points as $point)
                        <div class="list-group-item list-group-item-action">
                            <div class="row" id='point-{{ $point->getKey() }}'>
                                <div class='col-lg-4'>
                                    <div class='row'>
                                        <div class='col-lg'>{{ $point->getFullStreet() }}</div>
                                    </div>
                                    <div class='row '>
                                        <div class='col-lg small text-muted'>{{ $point->getName() }}</div>
                                    </div>
                                </div>
                                <p class="col-lg mb-lg-0 d-flex align-items-center">
                                    {{ $point->getPhone() }}
                                </p>
                                <p class="col-lg mb-lg-0 d-flex align-items-center">
                                    <a href="mailto:{{ $point->getEmail() }}">{{ $point->getEmail() }}</a>
                                </p>
                                <div class="col-lg d-flex align-items-center justify-content-end">
                                    <form action='' style='display: none;'></form>
                                    <div class='btn-group' role='group'>
                                        <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                           href="{{ route('points.show', [$organization, $point]) }}" role='button' class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->canUpdatePoints())
                                            <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                               href="{{ route('points.edit', [$organization, $point]) }}" role='button' class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(Auth::user()->canDeletePoints())
                                            {{ Form::open(['url' => route('points.destroy', [$organization, $point]), 'method' => 'delete', 'class' => 'btn btn-danger',
												'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
												 'onclick' => 'event.preventDefault(); if (!confirm("Удалить точку?")) return; this.submit()']) }}
                                            <i class="far fa-trash-alt"></i>
                                            {{ Form::close() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class='col-lg'>
                            <p>Адреса отсутствуют</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $points,
        'frd' => $frd
    ])
@stop
