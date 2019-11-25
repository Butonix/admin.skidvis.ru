@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                {{ Form::open(['url' => route('holidays.store'), 'method' => 'post', 'class' => 'form-group onSubmitAjax']) }}
                @include('products.holidays._form')
                @if (Auth::user()->canCreateHolidays())
                    <div class="row mx-0 justify-content-end mb-lg-0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" onclick='return confirm("Создать праздник?")'>
                                Создать
                            </button>
                        </div>
                    </div>
                @endif
                {{ Form::close() }}
            </div>
            <div class='col-lg-6'>
                @include('products.holidays.components._lastCreatedHolidays')
            </div>
        </div>
    </div>
@stop
