<?php
/**
 * @var \App\Models\Articles\ArticleLabel $label
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='category'>
            <div class='row ml-0 justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($label, ['url' => route('article-labels.update', $label), 'method' => 'patch', 'class' => 'form-group']) }}
                    <div class='row ml-0'>
                        <div class='col-lg-12 image-container' style='display: none;'>
                            <div id='image-container-store' data-action='{{ route('categories.image.store') }}'></div>
                        </div>
                        <div class='col-lg-3 mr-3'>
                            <div class='row'>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-label-file'>
                                            <div class='icon-label-file__wrapper' style='background-image: url({{ $label->getImageLink() }});'>
                                                <div class='icon-label-file__body'>
                                                    <label for='empty-image-input'>
                                                        <i class="fas fa-camera"></i>
                                                    </label>
                                                    <input accept='image' type='file' class='js-images-preview icon-label-file__body__preview' id='empty-image-input' data-check-aspect-ratio='1:1'
                                                           data-check-svg='true' data-image-type='icon'>
                                                    <div class='load-module-image'></div>
                                                    <input type='hidden' name='icon' class='js-image-base64' id='image-input-icon--hidden'>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='row justify-content-center mt-1'>
                                            <div class='col-lg-12 text-center' style='line-height: .85rem;'>
                                                <small class='text-muted category__icon--svg'>
                                                    SVG
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg'>
                            @include('articles.labels._form')
                            <div class='row mx-0 mb-3 justify-content-end'>
                                <div class="btn-group">
                                    <button type='submit' class="btn btn-success" onclick='return confirm("Обновить лейбл?")'>
                                        Сохранить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@stop
