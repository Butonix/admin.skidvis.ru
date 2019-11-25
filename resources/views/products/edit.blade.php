<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 * @var \App\Models\Products\Product           $product
 * @var \App\Models\Files\Image                $image
 * @var \App\Models\Products\Tag               $tag
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        {{ Form::open(['url' => route('products.update', [$organization, $product]), 'method' => 'patch', 'class' => 'js-form-validation']) }}
        <div class='product'>
            <div class='row mx-0 mb-3 justify-content-end'>
                <div class="btn-group">
                    <button type='submit' class="btn btn-success" onclick='return confirm("Обновить акцию?")'>
                        Сохранить
                    </button>
                </div>
            </div>
            <div class='row justify-content-between mb-3 '>
                {{--Блок с изображениями акции--}}
                <div class='col-lg-8 order-2 order-sm-2 order-md-2 order-lg-1 mt-4 mt-lg-0'>
                    <div class='row'>
                        <div class='col-lg-12 image-container'>
                            {{--Блок для хранение ссылки на сохранение изображений--}}
                            <div id='image-container-store' data-action='{{ route('products.image.store2') }}' style='display: none'></div>
                            {{--Слайдер с изображениями--}}
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @for ($i = 0; $i < $coversCount; $i++)
                                        <li data-target="#carouselExampleIndicators" data-slide-to="{{ $i }}" class="{{ ($i === 0) ? 'active' : '' }}"></li>
                                    @endfor
                                    @if ($coversCount === 0)
                                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                    @endif
                                </ol>
                                <div class="carousel-inner">
                                    @forelse($coversLinks as $image)
                                        <div class="carousel-item {{ ($loop->first) ? 'active' : '' }} product__cover" style='background-image: url({{ $image['src'] }});'></div>
                                    @empty
                                        <div class="carousel-item active w-100 organization__cover"
                                             style='background-image: url(data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=);'>
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
                            {{--Превьюхи изображений--}}
                            <div class='thumb-file'>
                                @forelse($coversLinks as $image)
                                    <div class='thumb-file__wrapper'>
                                        <div class='thumb-file__body' style='background-image: url({{ $image['src'] }}); border-style: none'>
                                            <label for='image-input-{{ $loop->index }}'>
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            {{--<label for='image-input-{{ now()->timestamp }}' class='mb-0 w-100 h-100 d-flex flex-row align-items-center justify-content-center'></label>--}}
                                            <input accept='image' type='file' class='js-images-preview thumb-file__body__preview' id='image-input-{{ $loop->index }}'>
                                            <div class='load-module-image'></div>
                                            <input type='hidden' name='images[{{$loop->index}}][id]' value='{{ $image['id'] }}'>
                                            @foreach($image as $width => $imageLess)
                                                @if ($width !== 'src' && $width !== 'id')
                                                    <input type='hidden' name='images[{{$loop->parent->index}}][{{ $width }}][id]' value='{{ $imageLess['id'] }}'>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                                <div class='thumb-file__wrapper'>
                                    <div class='thumb-file__body thumb-file__body--empty'>
                                        <label for='image-input-{{ now()->timestamp }}'>
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        {{--<label for='image-input-{{ now()->timestamp }}' class='mb-0 w-100 h-100 d-flex flex-row align-items-center justify-content-center'></label>--}}
                                        <input accept='image' type='file' class='js-images-preview thumb-file__body__preview' id='image-input-{{ now()->timestamp }}'>
                                        <div class='load-module-image'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg-12 mt-3'>
                            {{--Блок с аватаром организации и названием акции--}}
                            <div class='row ml-lg-0'>
                                <div class='col-lg-auto mr-3'>
                                    <div class='row justify-content-center'>
                                        <div class='organization__avatar'>
                                            <img src="{{ $organization->getAvatarLink() }}" class="img-fluid rounded img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class='col-lg mt-lg-0 mt-2'>
                                    @include('forms._textarea',[
										'name' => 'name',
										'class' => '',
										'required' => true,
										'placeholder' => 'Название акции',
										'value' => $product->getName(),
										'attributes' => [
											'style' => 'font-size: 24px; font-weight: bold;',
											'rows' => 5
										]
									])
                                </div>
                            </div>
                            {{--Блок с тегами--}}
                            <div class='row ml-lg-0 mt-3'>
                                <div class='col-lg-3 d-flex align-items-center'>
                                    Акции по тегам
                                </div>
                                <div class='col-lg mt-3'>
                                    @include('forms._select', [
										'class' => 'select--multiple',
										'name' => 'tags[]',
										'list' => $tagsList,
										'value' => (isset($product)) ? $chosenTags : null,
										'required' => true,
										'attributes' => [
											'style' => 'margin-bottom: -2px',
											'multiple' => 'multiple',
											'disabled' => (isset($readonly)) ? $readonly : false
										]
									])
                                </div>
                            </div>
                            {{--Блок с категориями--}}
                            <div class='row ml-lg-0'>
                                <div class='col-lg-3 d-flex align-items-center'>
                                    Категории
                                </div>
                                <div class='col-lg mt-3'>
                                    @include('forms._select', [
										'class' => '',
										'name' => 'category',
										'list' => $categoriesList,
										'value' => (isset($product)) ? $chosenCategories : null,
										'required' => true,
										'attributes' => [
											'style' => 'margin-bottom: -2px',
											'disabled' => (isset($readonly)) ? $readonly : false
										]
									])
                                </div>
                            </div>
                            {{--Блок с категориями--}}
                            <div class='row ml-lg-0'>
                                <div class='col-lg-3 pt-4'>
                                    Адреса
                                </div>
                                <div class='col-lg mt-3'>
                                    @include('forms._select', [
										'class' => 'select--multiple',
										'name' => 'points[]',
										'list' => $pointsList,
										'value' => (isset($product)) ? $chosenPoints : null,
										'text' => 'Обязательное поле',
										'required' => true,
										'attributes' => [
											'style' => 'margin-bottom: -2px',
											'multiple' => 'multiple',
											'disabled' => (isset($readonly)) ? $readonly : false
										]
									])
                                </div>
                            </div>
                            {{--Блок с категориями--}}
                            <div class='row ml-lg-0'>
                                <div class='col-lg-3 pt-4'>
                                    Описание
                                </div>
                                <div class='col-lg mt-3'>
                                    @include('forms._textarea',[
										'name' => 'description',
										'class' => 'w-100',
                                        'value' => $product->getDescription(),
										'placeholder' => 'Описание акции',
										'text' => 'Обязательное поле',
										'required' => true,
										'attributes' => [
											'rows' => 5
										]
									])
                                </div>
                            </div>
                            {{--Блок с категориями--}}
                            <div class='row ml-lg-0'>
                                <div class='col-lg-3 pt-4'>
                                    Условия
                                </div>
                                <div class='col-lg mt-3'>
                                    @include('forms._textarea',[
										'name' => 'conditions',
										'class' => 'w-100',
                                        'value' => $product->getConditions(),
										'placeholder' => 'Условия акции',
										'text' => 'Обязательное поле',
										'required' => true,
										'attributes' => [
											'rows' => 5
										]
									])
                                </div>
                            </div>
                            {{--Блок с условиями/описанием--}}
                            {{--<div class='row mx-0 mt-lg-5 mt-2'>--}}
                            {{--<ul class="nav  nav-tabs w-100 d-flex flex-row justify-content-between" id="myTab" role="tablist">--}}
                            {{--<li class="nav-item">--}}
                            {{--<a class="nav-link active" id="conditions-tab" data-toggle="tab" href="#conditions" role="tab" aria-controls="conditions" aria-selected="true">Условия</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="false">Описание</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false">Адреса</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Отзывы</a>--}}
                            {{--</li>--}}
                            {{--</ul>--}}
                            {{--<div class="tab-content mt-2 w-100" id="myTabContent">--}}
                            {{--<div class="tab-pane fade show active" id="conditions" role="tabpanel" aria-labelledby="conditions-tab">--}}
                            {{--@include('forms._textarea',[--}}
                            {{--'name' => 'conditions',--}}
                            {{--'class' => 'w-100',--}}
                            {{--'placeholder' => 'Условия акции',--}}
                            {{--'value' => $product->getConditions(),--}}
                            {{--'required' => true,--}}
                            {{--'attributes' => [--}}
                            {{--'rows' => 5--}}
                            {{--]--}}
                            {{--])--}}
                            {{--</div>--}}
                            {{--<div class="tab-pane fade" id="description" role="tabpanel" aria-labelledby="description-tab">--}}
                            {{--@include('forms._textarea',[--}}
                            {{--'name' => 'description',--}}
                            {{--'class' => 'w-100',--}}
                            {{--'placeholder' => 'Описание акции',--}}
                            {{--'value' => $product->getDescription(),--}}
                            {{--'required' => true,--}}
                            {{--'attributes' => [--}}
                            {{--'rows' => 5--}}
                            {{--]--}}
                            {{--])--}}
                            {{--</div>--}}
                            {{--<div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">--}}
                            {{--@include('forms._select', [--}}
                            {{--'class' => 'select--multiple',--}}
                            {{--'name' => 'points[]',--}}
                            {{--'list' => $pointsList,--}}
                            {{--'value' => (isset($product)) ? $chosenPoints : null,--}}
                            {{--'required' => true,--}}
                            {{--'attributes' => [--}}
                            {{--'style' => 'margin-bottom: -2px',--}}
                            {{--'multiple' => 'multiple',--}}
                            {{--'disabled' => (isset($readonly)) ? $readonly : false--}}
                            {{--]--}}
                            {{--])--}}
                            {{--</div>--}}
                            {{--<div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">--}}
                            {{--...--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
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
                    <div class='row product__time mt-2'>
                        <div class='col-lg-12'>
                            <div class="form-group row justify-content-between {{ (isset($errors) && $errors->has('start_at')) ? ' has-danger' : '' }}">
                                <label class="col-lg-2 col-md-2 col-sm-2 col-2 col-form-label text-right" for="input_text_start_at">
                                    С
                                </label>
                                <div class="col-lg col-md col-sm col mb-lg-0 mb-2">
                                    {{ Form::input('date', 'start_at', $product->getStartAt()->toDateString(), [
										'required' => true,
										'id' => 'input_date_start_at',
										'class' => 'form-control ' .((isset($errors) && $errors->has('start_at')) ? ' is-invalid ' : ''),
									]) }}
                                    @if(isset($feedback) || (isset($errors) && $errors->has('start_at') === true))
                                        <div class="invalid-feedback">{{ $feedback ?? $errors->first('start_at') }}</div>
                                    @endif
                                    <small class="form-text text-muted">
                                        Обязательное поле
                                    </small>
                                </div>
                            </div>
                            <div class="form-group row justify-content-between {{ (isset($errors) && $errors->has('start_at')) ? ' has-danger' : '' }}">
                                <label class="col-lg-2 col-md-2 col-sm-2 col-2 col-form-label text-right" for="input_text_start_at">
                                    По
                                </label>
                                <div class="col-lg col-md col-sm col mb-lg-0 mb-2">
                                    {{ Form::input('date', 'end_at', (!is_null($product->getEndAt())) ? $product->getEndAt()->toDateString() : null, [
										'id' => 'input_date_end_at',
										'class' => 'form-control ' .((isset($errors) && $errors->has('end_at')) ? ' is-invalid ' : ''),
									]) }}
                                    @if(isset($feedback) || (isset($errors) && $errors->has('end_at') === true))
                                        <div class="invalid-feedback">{{ $feedback ?? $errors->first('end_at') }}</div>
                                    @endif
                                    <small class="form-text text-muted">Если акция бессрочная, ничего не указывайте в
                                        данном поле
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-lg'>
                            Скидка
                        </div>
                    </div>
                    <div class='row product__time mt-2'>
                        <div class='col-lg-12'>
                            <div class="form-group row justify-content-between {{ (isset($errors) && $errors->has('origin_price')) ? ' has-danger' : '' }}">
                                <label class="col-lg-2 col-md-2 col-sm-2 col-2 col-form-label text-right" for="input_text_start_at">

                                </label>
                                <div class="col-lg col-md col-sm col mb-lg-0 mb-2">
                                    {{ Form::input('number', 'origin_price', $product->getOriginPrice(), [
										'required' => true,
										'id' => 'input_date_origin_price',
										'class' => 'form-control ' .((isset($errors) && $errors->has('origin_price')) ? ' is-invalid ' : ''),
									]) }}
                                    @if(isset($feedback) || (isset($errors) && $errors->has('origin_price') === true))
                                        <div class="invalid-feedback">{{ $feedback ?? $errors->first('origin_price') }}</div>
                                    @endif
                                    <small class="form-text text-muted">Обязательное поле</small>
                                    <small class="form-text text-muted">Оригинальная цена, руб</small>
                                </div>
                            </div>
                            <div class="form-group row justify-content-between {{ (isset($errors) && $errors->has('value')) ? ' has-danger' : '' }}">
                                <label class="col-lg-2 col-md-2 col-sm-2 col-2 col-form-label text-right" for="input_text_start_at">

                                </label>
                                <div class="col-lg col-md col-sm col mb-lg-0 mb-2">
                                    {{ Form::input('number', 'value', $product->getValue(), [
										'required' => true,
										'id' => 'input_date_value',
										'class' => 'form-control ' .((isset($errors) && $errors->has('value')) ? ' is-invalid ' : ''),
									]) }}
                                    @if(isset($feedback) || (isset($errors) && $errors->has('value') === true))
                                        <div class="invalid-feedback">{{ $feedback ?? $errors->first('value') }}</div>
                                    @endif
                                    <small class="form-text text-muted">Обязательное поле</small>
                                    <small class="form-text text-muted">Размер скидки, %</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@stop
