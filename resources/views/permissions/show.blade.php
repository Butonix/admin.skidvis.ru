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
                        <p>Псевдоним {{ $permission->getName() }}</p>
                        <p class="h5">Описание</p>
                        <p>{{ $permission->getDescription() }}</p>
                        <br>
                        <a class="btn btn-primary" href="{{ route('permissions.edit', $permission) }}">Редактировать</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
