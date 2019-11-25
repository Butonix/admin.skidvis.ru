<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='organization'>
            @if (Auth::user()->canUpdateOrganizations())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('organizations.edit', $organization) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center mb-3'>
                <div class='col-lg-12 image-container'>
                    <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @for ($i = 0; $i < $coversCount; $i++)
                                <li data-target="#carouselExampleIndicators" data-slide-to="{{ $i }}" class="{{ ($i === 0) ? 'active' : '' }}"></li>
                            @endfor
                        </ol>
                        <div class="carousel-inner">
                            @forelse($coversLinks as $key => $image)
                                <div class="carousel-item {{ ($key === 0) ? 'active' : '' }} organization__cover" style='background-image: url({{ $image['src'] }});'>
                                    {{--<img src="{{ $image->getPublishPath() }}" class="d-block w-100" alt="...">--}}
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <img src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" class="w-100">
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
            </div>
            <div class='row ml-lg-0 ml-n2 justify-content-between'>
                <div class='col-lg-auto mr-3'>
                    <div class='row justify-content-center'>
                        <div class=' organization__avatar{{ ($organization->getAvatar()) ? '' : '--empty' }}'
                             style='cursor: default; background-image: url({{ $organization->getAvatarLink() }});'>
                            @if(!$organization->getAvatar())
                                <label for='image-input-avatar' class='mb-0 w-100 h-100 d-flex flex-row align-items-center justify-content-center'></label>
                            @endif
                        </div>
                    </div>
                </div>
                <div class='col-lg'>
                    {{ Form::model($organization, ['class' => 'form-group']) }}
                    @include('organizations._form', [
                        'readonly' => true,
                        'displayTypeSchedule' => false
                    ])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
