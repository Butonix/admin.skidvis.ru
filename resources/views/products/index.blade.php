<?php
/**
 * @var \App\Models\Products\Product $product
 */
?>
@extends('layouts.app')

@section('content')
    <div class="js-index">
        @include('products.filters._filters', [
            'frd' => $frd,
            'tagsList' => $tagsList,
            'categoriesList' => $categoriesList
        ])
        <div class="container pt-lg-3 pb-lg-5 px-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class='row no-gutters justify-content-end'>
                        <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2 mr-2">
                            <button type='button' class="btn btn-secondary" style='padding-top: .7rem; padding-bottom: .7rem;'
                                    data-tooltip='true' data-placement='bottom' title='Фильтры' id='find-fields-open-button'>
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                        <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2">
                            <a href='{{ route('products.create', $organization) }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                               data-tooltip='true' data-placement='bottom' title='Новая акция'>
                                Добавить <i class="fas fa-tags"></i>
                            </a>
                        </div>
                    </div>
                    <div class='row'>
                        @forelse ($products as $product)
                            <div class='col-lg-4 col-pr'>
                                <div>
                                    <div class='product-card'>
                                        <div class='product-card__wrapper'>
                                            <div class='product-card__img'>
                                                <div class='product-card__img__content bg-cover lazyload' data-src='{{ $product->getFirstSliderImageLink() }}'
                                                     style='background-image: url("data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=");'>
                                                </div>
                                                @if($product->isUnpublished())
                                                    <div class='product-card__img__unpublished'>
                                                        Не опубликовано
                                                    </div>
                                                @endif
                                            </div>
                                            <div class='product-card__body'>
                                                <div class='product-card__body__name'>{{ $product->getName() }}</div>
                                                <div class='product-card__body__data'>
                                                    <div>
                                                        <i class="far fa-eye"></i> {{ $product->getViews() }}
                                                    </div>
                                                    <div>
                                                        {{ $product->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div class='product-card__body__buttons'>
                                                    <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                                       href="{{ route('products.show', [$organization, $product]) }}" role='button' class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                       href="{{ route('products.edit', [$organization, $product]) }}" role='button' class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    {{ Form::open(['url' => route('products.destroy', [$organization, $product]), 'method' => 'delete', 'class' => 'btn btn-danger',
													'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
													'onclick' => 'event.preventDefault(); if (!confirm("Удалить акцию?")) return; this.submit()']) }}
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
                                <p>Акции отсутствуют</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @include('forms._pagination', [
			'elements' => $products,
			'frd' => $frd
		])
    </div>
@stop
