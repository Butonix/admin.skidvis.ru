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
                {{ Form::open(['url' => route('users.store'), 'method' => 'post', 'class' => 'form-group']) }}
                @include('users.components._form', [
                    'createPassword' => true,
                    'rolesNeed' => true
                ])
                <div class="form-group text-right mb-lg-0">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            Сохранить
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
