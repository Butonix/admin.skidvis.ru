<?php
/**
 * @var \App\Models\Products\Category $category
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='category'>
            <div class='row ml-0 justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($category, ['url' => route('categories.update', $category), 'method' => 'patch', 'class' => 'form-group']) }}
                    <div class='row ml-0'>
                        <div class='col-lg-3 mr-3'>
                            <div class='row categories-images {{ ($category->forProducts()) ? 'active' : '' }}'>
                                <div class='col-lg-12 image-container' style='display: none;'>
                                    <div id='image-container-store' data-action='{{ route('categories.image.store') }}'></div>
                                </div>
                                <div class='col-lg-12 mb-3'>
                                    <div class='icon-file'>
                                        <div class='icon-file__wrapper'>
                                            <div class='icon-file__body' style='background-image: url({{ $category->getEmptyImageLink() }});  box-shadow: none'>
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
                                    <div class='icon-file'>
                                        <div class='icon-file__wrapper'>
                                            <div class='icon-file__body' style='background-image: url({{ $category->getImageLink() }}); box-shadow: none'>
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
                                    <div class='icon-file'>
                                        <div class='icon-file__wrapper'>
                                            <div class='icon-file__body' style='background-image: url({{ $category->getActiveImageLink() }}); box-shadow: none'>
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
                                    <div class='icon-file'>
                                        <div class='icon-file__wrapper'>
                                            <div class='icon-file__body' style='background-image: url({{ $category->getBusinessImageLink() }}); box-shadow: none'>
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
                                    <div class='icon-file'>
                                        <div class='icon-file__wrapper'>
                                            <div class='icon-file__body' style='background-image: url({{ $category->getBusinessActiveImageLink() }}); box-shadow: none'>
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
                            @if(Auth::user()->canUpdateCategories())
                                <div class='row mx-0 mb-3 justify-content-end'>
                                    <div class="btn-group">
                                        <button type='submit' class="btn btn-success" onclick='return confirm("Обновить категорию?")'>
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@stop
