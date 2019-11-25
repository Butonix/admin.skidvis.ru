<?php
/**
 * @var \App\Models\Users\User $user
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container pt-lg-3 pb-lg-5 px-0">
        <div class='row justify-content-center'>
            @if (auth()->id() === $user->getKey() || Auth::user()->hasRole(['super_administrator', 'technical_administrator', 'administrator']))
                <div class='col-lg-6'>
                    {{ Form::open(['url' => (Auth::user()->hasRole(['super_administrator', 'technical_administrator', 'administrator'])) ? route('users.edit.password.update', $user) : route('cabinet.update.password'),
                        'method' => 'patch', 'class' => 'form-group onSubmitAjax js-confirm-password']) }}
                    @include('forms._input', [
						'type' => 'password',
						'label' => 'Новый пароль',
						'name' => 'password',
						'required' => true
					])
                    @include('forms._input', [
						'type' => 'password',
						'label' => 'Подтвердите пароль',
						'name' => 'password_confirm',
						'required' => true
					])
                    <div class='row'>
                        <div class='col-lg text-right'>
                            <div class='btn-group'>
                                <button type='submit' class='btn btn-primary' onclick='return confirm("Обновить пароль?")'>Обновить пароль</button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            @else
            @endif
        </div>
    </div>
@stop
