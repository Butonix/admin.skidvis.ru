<?php
/**
 * @var \App\Models\Users\Role $role
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pt-lg-3 pb-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">{{ $role->getDisplayName() }}</div>
                    <div class="card-body">
                        <p>Псевдоним {{ $role->getName() }}</p>
                        <p class="h5">Описание</p>
                        <p>{{ $role->getDescription() }}</p>
                        <br>
                        <a class="btn btn-primary" href="{{ route('roles.edit', $role) }}">Редактировать</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
