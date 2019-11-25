@extends('layouts.app')

@section('content')
    @include('products.tags.filters._filters', [
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
                            @if(Auth::user()->canCreateTags())
                                <div class="col-lg-auto col-md-auto col-sm-auto col-auto pl-lg-2 pl-1">
                                    <a href='{{ route('tags.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                                       data-tooltip='true' data-placement='bottom' title='Новый тег'>
                                        Добавить <i class="fas fa-hashtag"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class='row mt-3'>
                    @forelse ($tags as $tag)
                        <div class='col-lg-2 col-md-3 col-sm-4 col col-pr'>
                            <div>
                                <div class='tags-card'>
                                    <div class='tags-card__wrapper'>
                                        <div class='tags-card__body'>
                                            <div class='tags-card__body__name'>{{ $tag->getName() }}</div>
                                            <div class='tags-card__body__buttons'>
                                                <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                                   href="{{ route('tags.show', $tag) }}" role='button' class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(Auth::user()->canUpdateTags())
                                                    <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                       href="{{ route('tags.edit', $tag) }}" role='button' class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(Auth::user()->canDeleteTags())
                                                    {{ Form::open(['url' => route('tags.destroy', $tag), 'method' => 'delete', 'class' => 'btn btn-danger',
													'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
													'onclick' => 'event.preventDefault(); if (!confirm("Удалить тег?")) return; this.submit()']) }}
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
                            <p>Теги отсутствуют</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $tags,
        'frd' => $frd
    ])
@stop
