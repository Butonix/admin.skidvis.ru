<?php
/**
 * @var \App\Models\Products\Category $category
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='row justify-content-between'>
            <div class='col-lg-6'>
                {{ Form::open(['url' => route('categories.store'), 'method' => 'post', 'class' => 'form-group', 'enctype' => "multipart/form-data"]) }}
                <div class='row'>
                    <div class='col-lg-3 mr-3'>
                        <div class='row categories-images'>
                            <div class='col-lg-12 image-container' style='display: none;'>
                                <div id='image-container-store' data-action='{{ route('categories.image.store') }}'></div>
                            </div>
                            <div class='col-lg-12 mb-3'>
                                {{--Превьюхи изображений--}}
                                <div class='icon-file'>
                                    <div class='icon-file__wrapper'>
                                        <div class='icon-file__body icon-file__body--empty'>
                                            <label for='empty-image-input'>
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input accept='image' type='file' class='js-images-preview icon-file__body__preview' id='empty-image-input' data-check-aspect-ratio='1:1'
                                                   data-check-svg='true' data-image-type='icon'>
                                            <div class='load-module-image'></div>
                                            <input type='hidden' name='icon[empty_image_id]' class='js-image-base64' id='image-input-icon--hidden'>
                                        </div>
                                    </div>
                                </div>
                                <div class='row justify-content-center mb-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted'>
                                            Пустая иконка
                                        </small>
                                    </div>
                                </div>
                                <div class='row justify-content-center mt-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted category__icon--svg'>
                                            SVG
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-12 mb-3'>
                                {{--Превьюхи изображений--}}
                                <div class='icon-file'>
                                    <div class='icon-file__wrapper'>
                                        <div class='icon-file__body icon-file__body--empty'>
                                            <label for='image-input'>
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input accept='image' type='file' class='js-images-preview icon-file__body__preview' id='image-input' data-check-aspect-ratio='1:1'
                                                   data-check-svg='true' data-image-type='icon'>
                                            <div class='load-module-image'></div>
                                            <input type='hidden' name='icon[image_id]' class='js-image-base64' id='image-input-icon--hidden'>
                                        </div>
                                    </div>
                                </div>
                                <div class='row justify-content-center mb-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted'>
                                            Обычная иконка
                                        </small>
                                    </div>
                                </div>
                                <div class='row justify-content-center mt-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted category__icon--svg'>
                                            SVG
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-12 mb-3'>
                                {{--Превьюхи изображений--}}
                                <div class='icon-file'>
                                    <div class='icon-file__wrapper'>
                                        <div class='icon-file__body icon-file__body--empty'>
                                            <label for='active-image-input'>
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input accept='image' type='file' class='js-images-preview icon-file__body__preview' id='active-image-input' data-check-aspect-ratio='1:1'
                                                   data-check-svg='true' data-image-type='icon'>
                                            <div class='load-module-image'></div>
                                            <input type='hidden' name='icon[active_image_id]' class='js-image-base64' id='image-input-icon--hidden'>
                                        </div>
                                    </div>
                                </div>
                                <div class='row justify-content-center mb-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted'>
                                            Активная иконка
                                        </small>
                                    </div>
                                </div>
                                <div class='row justify-content-center mt-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted category__icon--svg'>
                                            SVG
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-12 mb-3'>
                                {{--Превьюхи изображений--}}
                                <div class='icon-file'>
                                    <div class='icon-file__wrapper'>
                                        <div class='icon-file__body icon-file__body--empty'>
                                            <label for='business-image-input'>
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input accept='image' type='file' class='js-images-preview icon-file__body__preview' id='business-image-input' data-check-aspect-ratio='1:1'
                                                   data-check-svg='true' data-image-type='icon'>
                                            <div class='load-module-image'></div>
                                            <input type='hidden' name='icon[business_image_id]' class='js-image-base64' id='image-input-icon--hidden'>
                                        </div>
                                    </div>
                                </div>
                                <div class='row justify-content-center mb-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted'>
                                            Бизнес-иконка, обычная
                                        </small>
                                    </div>
                                </div>
                                <div class='row justify-content-center mt-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted category__icon--svg'>
                                            SVG
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-12 mb-3'>
                                {{--Превьюхи изображений--}}
                                <div class='icon-file'>
                                    <div class='icon-file__wrapper'>
                                        <div class='icon-file__body icon-file__body--empty'>
                                            <label for='business-active-image-input'>
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input accept='image' type='file' class='js-images-preview icon-file__body__preview' id='business-active-image-input' data-check-aspect-ratio='1:1'
                                                   data-check-svg='true' data-image-type='icon'>
                                            <div class='load-module-image'></div>
                                            <input type='hidden' name='icon[business_active_image_id]' class='js-image-base64' id='image-input-icon--hidden'>
                                        </div>
                                    </div>
                                </div>
                                <div class='row justify-content-center mb-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted'>
                                            Бизнес-иконка, активная
                                        </small>
                                    </div>
                                </div>
                                <div class='row justify-content-center mt-0'>
                                    <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                        <small class='text-muted category__icon--svg'>
                                            SVG
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg'>
                        @include('products.categories._form')
                        @if(Auth::user()->canCreateCategories())
                            <div class='row mx-0 mb-3 justify-content-end'>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary" onclick='return confirm("Создать категорию?")'>
                                        Создать
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class='col-lg-4'>
                @include('products.categories.components._lastCreatedCategories')
            </div>
        </div>
    </div>
@stop
