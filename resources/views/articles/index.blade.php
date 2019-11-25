<?php
/**
 * @var \App\Models\Articles\Article $article
 */
?>
@extends('layouts.app')

@section('content')
    <div class="js-index">
        @include('articles.filters._filters', [
            'categoriesList' => $categoriesList,
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
                        @if(Auth::user()->canCreateArticle())
                            <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2">
                                <a href='{{ route('articles.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                                   data-tooltip='true' data-placement='bottom' title='Новая статья'>
                                    Добавить <i class="far fa-newspaper"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class='row mt-3'>
                        @forelse ($articles as $article)
                            <div class='col-lg-4 col-pr'>
                                <div>
                                    <div class='article-card {{ ($article->isActual()) ? 'article-card--actual' : '' }}'>
                                        <div class='article-card__wrapper'>
                                            <div class='article-card__img'>
                                                <div class='article-card__img__content bg-cover lazyload' data-src='{{ $article->getCoverLink() }}'
                                                     style='background-image: url("data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=");'>
                                                </div>
                                            </div>
                                            <div class='article-card__body'>
                                                <div class='article-card__body__small'>{{ ($article->isActual()) ? 'Актуальная:' : '' }}
                                                    &nbsp;
                                                </div>
                                                <div class='article-card__body__name'>{{ $article->getName() }}</div>
                                                <div class='article-card__body__data'>
                                                    <div>
                                                        <i class="far fa-eye"></i> {{ $article->getViews() }}
                                                    </div>
                                                    <div>
                                                        <i class="far fa-clock"></i>
                                                        Читать {{ $article->getReadTime() }} минут
                                                    </div>
                                                </div>
                                                <div class='article-card__body__buttons'>
                                                    <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                                       href="{{ route('articles.show', $article) }}" role='button' class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(Auth::user()->canUpdateArticle())
                                                        <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                           href="{{ route('articles.edit', $article) }}" role='button' class="btn btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->canDeleteArticle())
                                                        {{ Form::open(['url' => route('articles.destroy', $article), 'method' => 'delete', 'class' => 'btn btn-danger',
														'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
														'onclick' => 'event.preventDefault(); if (!confirm("Удалить статью?")) return; this.submit()']) }}
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
                                <p>Статьи отсутствуют</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $articles,
        'frd' => $frd
    ])
@stop
