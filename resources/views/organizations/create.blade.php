@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='organization'>
            {{ Form::open(['url' => route('organizations.store'), 'method' => 'post', 'class' => 'form-group', 'enctype' => 'multipart/form-data']) }}
            <div class='row mx-0 mb-3 justify-content-end'>
                <div class="btn-group">
                    <button type='submit' class="btn btn-success" onclick='return confirm("Создать организацию?")'>
                        Создать
                    </button>
                </div>
            </div>
            <div class='row justify-content-center mb-3'>
                <div class='col-lg-12 image-container'>
                    {{--Блок для хранение ссылки на сохранение изображений--}}
                    <div id='image-container-store' data-action='{{ route('organizations.image.store2') }}'></div>
                    {{--Блок с изображениями акции--}}
                    <div id="carouselExampleIndicators" class="carousel slide " data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        </ol>
                        <div class="carousel-inner">
                            <div class="carousel-item active w-100 organization__cover"
                                 style='background-image: url(data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=);'>
                            </div>
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
                        <div class='thumb-file__wrapper'>
                            <div class='thumb-file__body thumb-file__body--empty'>
                                <label for='image-input-{{ now()->timestamp }}'>
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input accept='image' type='file' class='js-images-preview thumb-file__body__preview' id='image-input-{{ now()->timestamp }}'>
                                <div class='load-module-image'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row ml-0 justify-content-between'>
                <div class='col-lg-auto mr-3'>
                    <div class='row justify-content-center'>
                        <div class='organization__avatar empty' style='border-style: none'>
                            <label for='image-input-avatar' class='mb-0 w-100 h-100 d-flex flex-row align-items-center justify-content-center'></label>
                            <input accept='image' type='file' class='js-images-preview' id='image-input-avatar' style='display: none !important;'>
                            <input type='hidden' class='js-image-base64' name='avatar' id='image-input-avatar--hidden'>
                        </div>
                    </div>
                </div>
                <div class='col-lg'>
                    @include('organizations._form', [
                        'addAvatarShow' => false,
                        'addCoverShow' => false,
                        'displayTypeSchedule' => true
                    ])
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@stop
