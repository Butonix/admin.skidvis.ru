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
                @forelse ($products as $product)
                    <div class='col-lg-4 col-pr'>
                        <div>
                            <div class='product-card {{ ($product->isPublished()) ? 'product-card--published' : '' }}'>
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
                                                Дата создания:
                                                <br>
                                                {{ $product->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class='product-card__body__buttons'>
                                            <a data-tooltip="true" data-placement="bottom" title="Просмотр"
                                               href="{{ route('products.show', [$product->getOrganization(), $product]) }}" role='button' class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                               href="{{ route('products.edit', [$product->getOrganization(), $product]) }}" role='button' class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{ Form::open(['url' => route('products.destroy', [$product->getOrganization(), $product]), 'method' => 'delete', 'class' => 'btn btn-danger',
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
                    <p>Акции отсутствуют</p>
                @endforelse
            </div>
        </div>
        <div class='row justify-content-center'>
            {{ $products->appends($frd)->links() }}
        </div>
    </div>

@stop
