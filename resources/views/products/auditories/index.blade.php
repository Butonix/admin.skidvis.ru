<?php
/**
 * @var \App\Models\Products\Auditory $auditory
 */
?>
@extends('layouts.app')

@section('content')
    @include('products.auditories.filters._filters', [
        'frd' => $frd
    ])
    <div class="container px-0">
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
                            @if(Auth::user()->canCreateAuditories())
                                <div class="col-lg-auto col-md-auto col-sm-auto col-auto pl-lg-2 pl-1">
                                    <a href='{{ route('auditories.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                                       data-tooltip='true' data-placement='bottom' title='Новая аудитория'>
                                        Добавить <i class="fas fa-users"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class='row mt-3'>
                    @forelse ($auditories as $auditory)
                        <div class='col-lg-2 col-md-3 col-sm-4 col col-pr'>
                            <div>
                                <div class='auditories-card'>
                                    <div class='auditories-card__wrapper'>
                                        <div class='auditories-card__body'>
                                            <div class='auditories-card__body__name'>{{ $auditory->getName() }}</div>
                                            @if(Auth::user()->canUpdateAuditories())
                                                <div class='auditories-card__body__data'>
                                                    <div>
                                                        <label class="col-lg form-check-label d-flex align-items-center">
                                                            {{ Form::open(['url' => route('auditories.favorite', $auditory), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}
                                                            @include('forms._checkbox',[
															'name' => 'is_favorite',
															'label' => 'Избранное',
															'checked' => $auditory->isFavorite(),
															])
                                                            {{ Form::close() }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class='auditories-card__body__buttons'>
                                                <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                                   href="{{ route('auditories.show', $auditory) }}" role='button' class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(Auth::user()->canUpdateAuditories())
                                                    <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                       href="{{ route('auditories.edit', $auditory) }}" role='button' class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(Auth::user()->canDeleteAuditories())
                                                    {{ Form::open(['url' => route('auditories.destroy', $auditory), 'method' => 'delete', 'class' => 'btn btn-danger',
													'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
													'onclick' => 'event.preventDefault(); if (!confirm("Удалить аудиторию?")) return; this.submit()']) }}
                                                        <i class="far fa-trash-alt"></i>
                                                    {{ Form::close() }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class='col-lg-12'>
                            <p>Аудитории отсутствуют</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $auditories,
        'frd' => $frd
    ])
@stop
