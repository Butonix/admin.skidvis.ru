<?php
/**
 * @var \App\Models\Users\User $user
 */
?>

@extends('layouts.app')

@section('content')
    <div class="container pt-lg-3 pb-lg-5 px-0">
        <div class='row justify-content-center'>
            <div class='col-lg-6'>
                {{ Form::model($provider, ['url' => route('auth-providers.update', $provider), 'method' => 'patch', 'class' => 'form-group onSubmitAjax']) }}
                @include('auth.components._providerForm', [
                    'isEditableSlug' => false
                ])
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
