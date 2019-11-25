@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                {{ Form::open(['url' => route('auditories.store'), 'method' => 'post', 'class' => 'form-group onSubmitAjax']) }}
                @include('products.auditories._form')
                @if (Auth::user()->canCreateAuditories())
                    <div class="row mx-0 justify-content-end mb-lg-0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" onclick='return confirm("Создать аудиторию?")'>
                                Создать
                            </button>
                        </div>
                    </div>
                @endif
                {{ Form::close() }}
            </div>
            <div class='col-lg-6'>
                @include('products.auditories.components._lastCreatedAuditories')
            </div>
        </div>
    </div>
@stop
