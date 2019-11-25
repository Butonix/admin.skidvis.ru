<?php
/**
 * @var \App\Models\Products\Holiday $holiday
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='holiday'>
            <div class='row ml-0 justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($holiday, ['url' => route('holidays.update', $holiday), 'method' => 'patch', 'class' => 'form-group']) }}
                    @include('products.holidays._form')
                    @if (Auth::user()->canUpdateHolidays())
                        <div class='row mx-0 mb-3 justify-content-end'>
                            <div class="btn-group">
                                <button type='submit' class="btn btn-success" onclick='return confirm("Обновить праздник?")'>
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    @endif
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
