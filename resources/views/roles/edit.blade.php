<?php
/**
 * @var \App\Models\Users\Role $role
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container pt-lg-3 pb-lg-5 px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                {{ Form::model($role, ['url' => route('roles.update', $role), 'method' => 'patch', 'class' => 'form-group onSubmitAjax']) }}
                @include('roles.components._form')
                <div class="form-group text-right mb-lg-0">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" onclick='return confirm("Обновить данные?")'>
                            Обновить данные
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
