@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                {{ Form::open(['url' => route('tags.store'), 'method' => 'post', 'class' => 'form-group onSubmitAjax']) }}
                @include('products.tags._form')
                @if (Auth::user()->canCreateTags())
                    <div class="row mx-0 justify-content-end mb-lg-0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" onclick='return confirm("Создать тег?")'>
                                Создать
                            </button>
                        </div>
                    </div>
                @endif
                {{ Form::close() }}
            </div>
            <div class='col-lg-6'>
                @include('products.tags.components._lastCreatedTags')
            </div>
        </div>
    </div>
@stop
