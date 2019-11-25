<?php
/**
 * @var \App\Models\Social\SocialNetwork $socialNetwork
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='social_network'>
            <div class='row mx-0 mb-3 justify-content-end'>
                <div class="btn-group">
                    <a role='button' class="btn btn-outline-primary" href='{{ route('social-networks.edit', $socialNetwork) }}'>
                        Редактировать
                    </a>
                </div>
            </div>
            <div class='row justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($socialNetwork, ['class' => 'form-group']) }}
                    @include('social.networks._form', [
                        'readonly' => true,
                    ])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
