<?php
/**
 * @var \App\Models\Articles\Article $article
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='article'>
            @if(Auth::user()->canUpdateArticle())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('articles.edit', $article) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            {{ Form::model($article, ['class' => 'form-group']) }}
            <div class='row justify-content-center mb-3'>
                <div class='col-lg-8 image-container'>
                    {{--Блок для хранение ссылки на сохранение изображений--}}
                    <div id='image-container-store' data-action='{{ route('articles.image.store') }}' style='display: none'></div>
                    {{--Блок с главным изображением поста--}}
                    <div class="cover-file">
                        <div class='cover-file__wrapper {{ (is_null($article->getCoverLink())) ? 'cover-file__wrapper--empty' : '' }}'
                             style='background-image: url({{ $article->getCoverLink() }});'>
                        </div>
                    </div>
                </div>
            </div>
            @include('articles._form', [
                'readonly' => true,
                'checkboxDisable' => true
			])
            {{ Form::close() }}
        </div>
    </div>
@stop
