<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='point'>
            @if(Auth::user()->canUpdatePoints())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('points.edit', [$organization, $point]) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center'>
                <div class='col-lg'>
                    {{ Form::model($point, ['class' => 'form-group']) }}
                    @include('organizations.points._form', [
                        'readonly' => true,
                        'showPointActive' => true,
                        'displayInheritanceSchedule' => true,
                        'disabledRadio' => true
                    ])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
