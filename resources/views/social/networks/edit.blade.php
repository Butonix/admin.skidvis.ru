<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='social_network'>
            <div class='row ml-0 justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($socialNetwork, ['url' => route('social-networks.update', $socialNetwork), 'method' => 'patch', 'class' => 'form-group']) }}
                    @include('social.networks._form')
                    <div class='row mx-0 mb-3 justify-content-end'>
                        <div class="btn-group">
                            <button type='submit' class="btn btn-success" onclick='return confirm("Обновить соц.сеть?")'>
                                Сохранить
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
