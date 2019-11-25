<?php
/**
 * @var \App\Models\Products\Auditory $auditory
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class='auditory'>
            <div class='row ml-0 justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($auditory, ['url' => route('auditories.update', $auditory), 'method' => 'patch', 'class' => 'form-group']) }}
                    @include('products.auditories._form')
                    @if (Auth::user()->canUpdateAuditories())
                        <div class='row mx-0 mb-3 justify-content-end'>
                            <div class="btn-group">
                                <button type='submit' class="btn btn-success" onclick='return confirm("Обновить аудиторию?")'>
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
