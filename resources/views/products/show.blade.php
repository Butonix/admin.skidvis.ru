<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 * @var \App\Models\Products\Product           $product
 * @var \App\Models\Files\Image                $image
 * @var \App\Models\Products\Tag               $tag
 * @var \App\Models\Organizations\Point        $point
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='product'>
            @if (Auth::user()->hasRole(['super_administrator', 'technical_administrator', 'administrator', 'management']))
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('products.edit', [$organization, $product]) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-between mb-3 '>
                {{--Блок с изображениями акции--}}
                <div class='col-lg-8 order-2 order-sm-2 order-md-2 order-lg-1 mt-4 mt-lg-0'>
                    <div class='row'>
                        <div class='col-lg-12 image-container'>
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @for ($i = 0; $i < $product->countImages(); $i++)
                                        <li data-target="#carouselExampleIndicators" data-slide-to="{{ $i }}" class="{{ ($i === 0) ? 'active' : '' }}"></li>
                                    @endfor
                                </ol>
                                <div class="carousel-inner">
                                    @forelse($product->getSliderImages() as $image)
                                        <div class="carousel-item {{ ($loop->first) ? 'active' : '' }} product__cover" style='background-image: url({{ $image->getPublishPath() }});'>
                                            {{--<img src="{{ $image->getPublishPath() }}" class="d-block w-100" alt="...">--}}
                                        </div>
                                    @empty
                                        <div class="carousel-item active">
                                            <img src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" class="d-block w-100" alt="...">
                                        </div>
                                    @endforelse
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class='col-lg-12'>
                            {{--Блок с аватаром организации и названием акции--}}
                            <div class='row ml-0 mt-3'>
                                <div class='col-lg-auto mr-3'>
                                    <div class='row justify-content-center'>
                                        <div class='organization__avatar'>
                                            <a href='{{ route('organizations.show', $organization) }}'>
                                                <img src="{{ $organization->getAvatarLink() }}" class="img-fluid rounded img-thumbnail">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-lg'>
                                    <p class='card-text' style='font-size: 24px; font-weight: bold;'>{{ $product->getName() }}</p>
                                </div>
                            </div>
                            {{--Блок с тегами--}}
                            <div class='row mt-3'>
                                <div class='col-lg-auto col-auto'>
                                    Акции по тегам
                                </div>
                                @forelse($product->getTags() as $tag)
                                    <div class='col-lg-auto col-auto'>
                                        <span class='px-3 py-2' style='background-color: #EEEEEE; border-radius: 115px'>
                                            {{ $tag->getName() }}
                                        </span>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                            {{--Блок с условиями/описанием--}}
                            <div class='row mx-0 mt-5'>
                                <ul class="nav  nav-tabs w-100 d-flex flex-row justify-content-between" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="conditions-tab" data-toggle="tab" href="#conditions" role="tab" aria-controls="conditions" aria-selected="true">Условия</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="false">Описание</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Отзывы</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-2 w-100" id="myTabContent">
                                    <div class="tab-pane fade show active" id="conditions" role="tabpanel" aria-labelledby="conditions-tab">
                                        {!! $product->getConditions() !!}
                                    </div>
                                    <div class="tab-pane fade" id="description" role="tabpanel" aria-labelledby="description-tab">
                                        {!! $product->getDescription() !!}
                                    </div>
                                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                        ...
                                    </div>
                                </div>
                            </div>
                            {{--Блок с адресами акции--}}
                            <div class='row ml-0 mt-4'>
                                <div class='col-lg-12'>
                                    <div class='row'>
                                        <h3>Акция по адресам</h3>
                                    </div>
                                    <div class='row mt-2'>
                                        <div class='col-lg-12'>
                                            @forelse($chosenPoints as $point)
                                                <div class='row'>
                                                    {{ $point->getFullStreet() . ', ' . $point->getName() }}
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--Блок с краткой информацией--}}
                <div class='col-lg-3 order-1 order-sm-1 order-md-1 order-lg-2'>
                    <div class='row'>
                        <div class='col-lg'>
                            Акция действует
                        </div>
                    </div>
                    <div class='row product__time'>
                        <div class='col-lg'>
                            {{ $product->getTimeAction() }}
                        </div>
                    </div>
                    <div class='row mt-2 mt-lg-5'>
                        <div class='col-lg'>
                            <span class='mr-2 text-muted' style='text-decoration: line-through'>{!! $product->getOriginPrice() . ' &#8381;' !!}</span> {!! $product->getDiscountString() !!}
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class='row justify-content-start'>--}}
            {{--<div class='col-lg-8'>--}}
            {{----}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
@stop
