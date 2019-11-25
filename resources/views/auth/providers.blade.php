<?php
/**
 * @var \App\Models\Users\Auth\AuthProvider $provider
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class='row no-gutters'>
                    <div class="text-right col-lg mb-lg-2 mt-lg-2 col-md mb-md-2 mt-md-2 col-sm mb-sm-2 mt-sm-2 col mb-2 mt-2">
                        <div class='btn-group' role='group'>
                            <button class="btn btn-success" style='padding-top: .475rem;' data-toggle="modal" data-target="#provider-create-modal"
                                    data-tooltip='true' data-placement='bottom' title='Новый сервис'>
                                Добавить <i class="fas fa-users"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class='row mt-3'>
                    @forelse ($providers as $provider)
                        <div class='col-lg-3 col-pr'>
                            <div>
                                <div class='provider-card'>
                                    <div class='provider-card__wrapper'>
                                        <div class='provider-card__img'>
                                            <div class='provider-card__img__content bg-cover lazyload' data-src='{{ $provider->getIconUrl() }}'>
                                            </div>
                                        </div>
                                        <div class='provider-card__body'>
                                            <div class='provider-card__body__name'>{{ $provider->getName() }}</div>
                                            <div class='provider-card__body__data'>
                                                <div>
                                                    <label class="col-lg-2 form-check-label d-flex align-items-center">
                                                        {{ Form::open(['url' => route('providers.set.published', $provider), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}
                                                        @include('forms._checkbox',[
															'name' => 'published',
															'label' => 'Активировать',
															'checked' => $provider->isPublished(),
														])
                                                        {{ Form::close() }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class='provider-card__body__buttons'>
                                                <a data-tooltip="true" data-placement="bottom" title="Редактирование"
                                                   href="{{ route('auth-providers.edit', $provider) }}" role='button' class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{ Form::open(['url' => route('auth-providers.destroy', $provider), 'method' => 'delete', 'class' => 'btn btn-danger',
												'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
												'onclick' => 'event.preventDefault(); if (!confirm("Удалить сервис авторизации?")) return; this.submit()']) }}
                                                <i class="far fa-trash-alt"></i>
                                                {{ Form::close() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class='col-lg-12'>
                            <p>Сервисы авторизации отсутствуют</p>
                        </div>
                    @endforelse
                </div>
                {{--<div class="list-group">--}}
                    {{--@forelse ($providers as $provider)--}}
                        {{--<div class="list-group-item list-group-item-action">--}}
                            {{--<div class="row" id='provider-{{ $provider->getKey() }}'>--}}
                                {{--<label class="col-lg-4 form-check-label d-flex align-items-center">--}}
                                    {{--@include('forms._checkbox',[--}}
										{{--'name' => 'providers[]',--}}
										{{--'value' => $provider->getKey(),--}}
										{{--'label' => $provider->getName() . '<span class="ml-2 logo--small"><img src="' . $provider->getIconUrl() . '"></span>',--}}
										{{--'checked' => false,--}}
									{{--])--}}
                                {{--</label>--}}
                                {{--<label class="col-lg-2 form-check-label d-flex align-items-center">--}}
                                    {{--{{ Form::open(['url' => route('providers.set.published', $provider), 'method' => 'patch', 'class' => 'onChangeSubmitAjax']) }}--}}
                                    {{--@include('forms._checkbox',[--}}
										{{--'name' => 'published',--}}
										{{--'label' => 'Активировать',--}}
						                {{--'checked' => $provider->isPublished(),--}}
									{{--])--}}
                                    {{--{{ Form::close() }}--}}
                                {{--</label>--}}
                                {{--<div class="col-lg d-flex align-items-center justify-content-end">--}}
                                    {{--<form action='' style='display: none;'></form>--}}
                                    {{--<div class='btn-group' role='group'>--}}
                                        {{--<a data-tooltip="true" data-placement="bottom" title="Редактирование"--}}
                                           {{--href="{{ route('auth-providers.edit', $provider) }}" role='button' class="btn btn-outline-primary">--}}
                                            {{--<i class="fas fa-edit"></i>--}}
                                        {{--</a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--@empty--}}
                        {{--<p>No providers</p>--}}
                    {{--@endforelse--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $providers,
        'frd' => $frd
    ])
    @include('auth.components.createProviderModal')
@stop
