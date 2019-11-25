<?php
/**
 * @var \App\Models\Products\Auditory $auditory
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class='auditory'>
            @if (Auth::user()->canUpdateAuditories())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('auditories.edit', $auditory) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($auditory, ['class' => 'form-group']) }}
                    @include('products.auditories._form', [
                        'readonly' => true,
                    ])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
