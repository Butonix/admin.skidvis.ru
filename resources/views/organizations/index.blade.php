<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    @include('organizations.filters._filters', [
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
                    @if(Auth::user()->canCreateOrganizations())
                        <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2">
                            <a href='{{ route('organizations.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                               data-tooltip='true' data-placement='bottom' title='Новая статья'>
                                Добавить <i class="far fa-newspaper"></i>
                            </a>
                        </div>
                    @endif
                </div>
                <div class='row mt-3'>
                    @forelse ($organizations as $organization)
                        <div class='col-lg-4 col-pr'>
                            <div>
                                <div class='organization-card'>
                                    <div class='organization-card__wrapper'>
                                        <div class='organization-card__img'>
                                            <div class='organization-card__img__content bg-cover lazyload' data-src='{{ $organization->getAvatarLink() }}'
                                                 style='background-image: url("data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=");'>
                                            </div>
                                            @if($organization->isUnpublished())
                                                <div class='organization-card__img__unpublished'>
                                                    Не опубликовано
                                                </div>
                                            @endif
                                        </div>
                                        <div class='organization-card__body'>
                                            <div class='organization-card__body__name'>{{ $organization->getName() }}</div>
                                            <div class='organization-card__body__data'>
                                                <div class='organization-card__body__small'>
                                                    {{ $organization->getPhone() }} &nbsp;
                                                </div>
                                                <div class='organization-card__body__small'>
                                                    {{ $organization->getEmail() }} &nbsp;
                                                </div>
                                            </div>
                                            <div class='organization-card__body__data'>
                                                <div>
                                                    Дата создания
                                                </div>
                                                <div>
                                                    {{ $organization->created_at->toDateString() }}
                                                </div>
                                            </div>
                                            <div class='organization-card__body__buttons'>
                                                <div>
                                                    <a data-tooltip="true" data-placement="bottom" title="Акции"
                                                       href="{{ route('products.index', $organization) }}" role='button' class="btn btn-outline-success">
                                                        <i class="fas fa-tags mr-1"></i>
                                                        <span class="badge badge-secondary">{{ $organization->getProductsCount() }}</span>
                                                    </a>
                                                    <a data-tooltip="true" data-placement="bottom" title="Адреса"
                                                       href="{{ route('points.index', $organization) }}" role='button' class="btn btn-outline-primary">
                                                        <i class="fas fa-store-alt mr-1"></i>
                                                        <span class="badge badge-secondary">{{ $organization->getPointsCount() }}</span>
                                                    </a>
                                                </div>
                                                <div>
                                                    <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                                       href="{{ route('organizations.show', $organization) }}" role='button' class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(Auth::user()->canUpdateOrganizations())
                                                        <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                           href="{{ route('organizations.edit', $organization) }}" role='button' class="btn btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->canDeleteOrganizations())
                                                        {{ Form::open(['url' => route('organizations.destroy', $organization), 'method' => 'delete', 'class' => 'btn btn-danger',
														'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
														'onclick' => 'event.preventDefault(); if (!confirm("Удалить организацию?")) return; this.submit()']) }}
                                                        <i class="far fa-trash-alt"></i>
                                                        {{ Form::close() }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class='col-lg-12'>
                            <p>Организации отсутствуют</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $organizations,
        'frd' => $frd
    ])
@stop
