<?php
/**
 * @var \App\Models\Products\Holiday $holiday
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='holiday'>
            @if (Auth::user()->canUpdateHolidays())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('holidays.edit', $holiday) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($holiday, ['class' => 'form-group']) }}
                    @include('products.holidays._form', [
                        'readonly' => true,
                    ])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
