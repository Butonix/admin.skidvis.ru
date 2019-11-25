@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='article'>
            {{ Form::open(['data-url' => route('articles.store'), 'data-method' => 'post', 'class' => 'form-group onSubmitAjaxQuill']) }}
            @if(Auth::user()->canCreateArticle())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <button type='submit' class="btn btn-success" onclick='return confirm("Создать пост?")'>
                            Создать
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
                        <div class='cover-file__wrapper'>
                            <div class='cover-file__body cover-file__body--empty'>
                                <label for='image-input-{{ now()->timestamp }}'>
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input accept='image' type='file' class='js-images-preview cover-file__body__preview' id='image-input-{{ now()->timestamp }}'>
                                <div class='load-module-image'></div>
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
