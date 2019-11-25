<?php
/**
 * @var \Illuminate\Notifications\DatabaseNotification $notification
 */
?>
@extends('layouts.app')

@section('content')
    <div class="js-index">
        @include('notifications.filters._filters', [
            'frd' => $frd
        ])
        <div class="container px-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class='row no-gutters justify-content-end'>
                        <div class="col-lg-auto mb-lg-2 mt-lg-2 col-md-auto mb-md-2 mt-md-2 col-sm-auto mb-sm-2 mt-sm-2 col-auto mb-2 mt-2 mr-2">
                            <button type='button' class="btn btn-secondary" style='padding-top: .7rem; padding-bottom: .7rem;'
                                    data-tooltip='true' data-placement='bottom' title='Фильтры' id='find-fields-open-button'>
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <div class='row '>
                        @forelse ($notifications as $notification)
                            <div class='col-lg-6 mb-3'>
                                <div class='notification {{ ($notification->unread()) ? 'notification--unread js-make-read' : '' }}'
                                     data-url='{{ route('notifications.make-as-read', $notification) }}' data-method='PATCH'>
                                    {{ Form::open(['url' => route('notifications.destroy', $notification), 'method' => 'delete', 'class' => 'notification__button notification__button--delete',
                                        'data-tooltip' => "true", 'data-placement' => "bottom", 'title' => "Удалить", 'style' => 'cursor: pointer',
                                        'onclick' => 'event.preventDefault(); if (!confirm("Удалить уведомление?")) return; this.submit()']) }}
                                        <i class="fas fa-times"></i>
                                    {{ Form::close() }}
                                    <div class='notification__body'>
                                        @if((isset($notification->data['feedbackType'])))
                                            <div class='notification__body__name'>
                                                {{ $notification->data['feedbackType'] }}
                                            </div>
                                        @endif
                                        @if((isset($notification->data['name'])))
                                            <div class='notification__body__text'>
                                                {{ $notification->data['name'] }}
                                            </div>
                                        @endif
                                        @if((isset($notification->data['phone'])))
                                            <div class='notification__body__text'>
                                                {{ $notification->data['phone'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class='col-lg-12'>
                                <p>Уведомления отсутствуют</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $notifications,
        'frd' => $frd
    ])
@stop
