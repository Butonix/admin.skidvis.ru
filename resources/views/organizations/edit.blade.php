<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='organization'>
            {{ Form::model($organization, ['url' => route('organizations.update', $organization), 'method' => 'patch', 'class' => 'form-group']) }}
            <div class='row mx-0 mb-3 justify-content-end'>
                <div class="btn-group">
                    <button type='submit' class="btn btn-success" onclick='return confirm("Обновить организацию?")'>
                        Сохранить
                    </button>
                </div>
            </div>
            <div class='row justify-content-center mb-3'>
                <div class='col-lg-12 image-container'>
                    {{--Блок для хранение ссылки на сохранение изображений--}}
                    <div id='image-container-store' data-action='{{ route('organizations.image.store2') }}' style='display: none'></div>
                    {{--Блок с изображениями акции--}}
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
                            @forelse($coversLinks as $key => $image)
                                <div class="carousel-item {{ ($key === 0) ? 'active' : '' }} organization__cover" style='background-image: url({{ $image['src'] }});'></div>
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
                        <div class='organization__avatar {{ ($organization->getAvatar()) ? '' : 'empty' }}' style='background-image: url({{ ($organization->getAvatar()) ? $organization->getAvatar()->getPublishPath() : '' }}); border-style: none'>
                            <label for='image-input-avatar' class='mb-0 w-100 h-100 d-flex flex-row align-items-center justify-content-center'></label>
                            <input accept='image' type='file' class='js-images-preview' id='image-input-avatar' style='display: none !important;'>
                            <input type='hidden' class='js-image-base64' name='avatar' id='image-input-avatar--hidden'>
                        </div>
                    </div>
                    <div class='row mt-2 mx-n3'>
                        <label class="form-control-label">
                            <span class='mr-2'>Социальные сети</span>
                            @isset($socialNetworks)
                                @forelse($socialNetworks as $socialNetwork)
                                    <img src="{{ $socialNetwork->getIconUrl() }}" width='24' height='24' class='mr-2'>
                                @empty
                                @endforelse
                            @endisset
                        </label>
                    </div>
                    <div class='row mb-2 mx-0'>
                        {{--@isset($socialNetworks)--}}
                        {{--@forelse($socialNetworks as $socialNetwork)--}}
                        {{--<div class='col-lg-6'>--}}
                        {{--@include('forms._input',[--}}
                        {{--'name' => 'social_networks[' . $socialNetwork->getKey() . ']',--}}
                        {{--'type' => 'text',--}}
                        {{--'label' => '<span class="mr-2 logo--small"><img src="' . $socialNetwork->getIconUrl() . '"></span>' . $socialNetwork->getName(),--}}
                        {{--'attributes' => [--}}
                        {{--'readonly' => (isset($readonly)) ? $readonly : false,--}}
                        {{--]--}}
                        {{--])--}}
                        {{--</div>--}}
                        {{--@empty--}}
                        {{--@endforelse--}}
                        {{--@endisset--}}
                        {{--@isset($socialAccounts)--}}
                        {{--@forelse($socialAccounts as $socialAccount)--}}
                        {{--<div class='col-lg-6'>--}}
                        {{--@include('forms._input',[--}}
                        {{--'name' => '',--}}
                        {{--'type' => 'text',--}}
                        {{--'label' => '<span class="mr-2 logo--small"><img src="' . $socialAccount->getSocialNetworkIcon() . '"></span>' . $socialAccount->getSocialNetworkName(),--}}
                        {{--'value' => $socialAccount->getLink(),--}}
                        {{--'attributes' => [--}}
                        {{--'readonly' => (isset($readonly)) ? $readonly : false,--}}
                        {{--]--}}
                        {{--])--}}
                        {{--</div>--}}
                        {{--@empty--}}
                        {{--@endforelse--}}
                        {{--@endisset--}}
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
