<?php
/**
 * @var \App\Models\Users\User $user
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-8'>
                {{ Form::model($user, ['url' => (Auth::user()->hasRole(['super_administrator', 'technical_administrator', 'administrator'])) ? route('users.update', $user) : route('cabinet.update'),
                    'method' => 'patch', 'class' => 'form-group onSubmitAjax']) }}
                @include('users.components._form')
                <div class="form-group mb-lg-0 row">
                    @if(Auth::user()->isSuperAdministrator())
                        <div class='col-auto col-md-auto col-sm-auto col-auto btn-group'>
                            <a href='{{ route('users.roles', $user) }}' class='btn btn-outline-success'>Роли</a>
                            <a href='{{ route('users.permissions', $user) }}' class='btn btn-outline-secondary'>Разрешения</a>
                            <a href='{{ route('users.organizations', $user) }}' class='btn btn-outline-dark'>Организации</a>
                        </div>
                    @endif
                    <div class="col-lg col-md col-sm col text-right">
                        <div class='btn-group'>
                            <a data-tooltip="true" data-placement="bottom" title="Смена пароля"
                               role='button' class="btn btn-outline-dark" href="{{ route('users.edit.password', $user) }}">
                                Сменить пароль
                            </a>
                            <button type="submit" class="btn btn-primary" onclick='return confirm("Обновить данные?")'>
                                Обновить данные
                            </button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
