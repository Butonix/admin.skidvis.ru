@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                {{ Form::open(['url' => route('social-networks.store'), 'method' => 'post', 'class' => 'form-group onSubmitAjax']) }}
                @include('social.networks._form')
                <div class="row mx-0 justify-content-end mb-lg-0">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" onclick='return confirm("Создать соц.сеть?")'>
                            Создать
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        @include('social.networks.components._lastCreatedTags')
    </div>
@stop
