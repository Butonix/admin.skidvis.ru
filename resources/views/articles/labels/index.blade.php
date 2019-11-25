<?php
/**
 * @var \App\Models\Articles\ArticleLabel $label
 */
?>
@extends('layouts.app')

@section('content')
    @include('articles.labels.filters._filters', [
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
                            <div class="col-lg-auto col-md-auto col-sm-auto col-auto pl-lg-2 pl-1">
                                <a href='{{ route('article-labels.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                                   data-tooltip='true' data-placement='bottom' title='Новый лейбл'>
                                    Добавить <i class="fas fa-tag"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    @forelse ($labels as $label)
                        <div class='col-lg-2 col-md-3 col-sm-4 col col-pr'>
                            <div>
                                <div class='article-label-card'>
                                    <div class='article-label-card__wrapper'>
                                        <div class='article-label-card__img'>
                                            <div class='article-label-card__img__content bg-cover lazyload' data-src='{{ $label->getImageLink() }}'>
                                            </div>
                                        </div>
                                        <div class='article-label-card__body'>
                                            <div class='article-label-card__body__name'>{{ $label->getName() }}</div>
                                            <div class='article-label-card__body__buttons'>
                                                <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                                   href="{{ route('article-labels.show', $label) }}" role='button' class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                   href="{{ route('article-labels.edit', $label) }}" role='button' class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{ Form::open(['url' => route('article-labels.destroy', $label), 'method' => 'delete', 'class' => 'btn btn-danger',
												'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
												'onclick' => 'event.preventDefault(); if (!confirm("Удалить лейбл?")) return; this.submit()']) }}
                                                <i class="far fa-trash-alt"></i>
                                                {{ Form::close() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class='col-lg-12'>
                            <p>Теги отсутствуют</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $labels,
        'frd' => $frd
    ])
@stop
