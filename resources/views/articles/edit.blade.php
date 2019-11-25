<?php
/**
 * @var \App\Models\Articles\Article $article
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='article'>
            {{ Form::model($article, ['data-url' => route('articles.update', $article), 'data-method' => 'patch', 'class' => 'form-group onSubmitAjaxQuill']) }}
            @if(Auth::user()->canUpdateArticle())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <button type='submit' class="btn btn-success" onclick='return confirm("Обновить статью?")'>
                            Сохранить
                        </button>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center mb-3'>
                <div class='col-lg-8 image-container'>
                    {{--Блок для хранение ссылки на сохранение изображений--}}
                    <div id='image-container-store' data-action='{{ route('articles.image.store') }}' style='display: none'></div>
                    {{--Блок с главным изображением поста--}}
                    <div class="cover-file">
                        <div class='cover-file__wrapper' style='background-image: url({{ $coverLink }});'>
                            <div class='cover-file__body cover-file__body--empty'>
                                <label for='image-input-{{ now()->timestamp }}'>
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input accept='image' type='file' class='js-images-preview cover-file__body__preview' id='image-input-{{ now()->timestamp }}'>
                                <div class='load-module-image'></div>
                                <input type='hidden' name='images[0][id]' value='{{ $coverId }}'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('articles._form', [
                'activeCategoriesSelect' => true
			])
            {{ Form::close() }}
        </div>
    </div>
@stop
