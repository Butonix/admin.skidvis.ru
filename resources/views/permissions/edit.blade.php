<?php
/**
 * @var \App\Models\Users\Permission $permission
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container pt-lg-3 pb-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">{{ $permission->getDisplayName() }}</div>
                    <div class="card-body">
                        {!! Form::model($permission, ['url' => route('permissions.update', $permission), 'method' => 'PATCH', 'class' => 'onSubmitAjax']) !!}
                        @include('permissions.components._form')
                        <div class="form-group mb-lg-0">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Подтвердите сохранение?')">
                                    Сохранить
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
