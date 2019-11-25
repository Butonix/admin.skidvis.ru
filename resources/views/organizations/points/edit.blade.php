<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='point'>
            <div class='row justify-content-center'>
                <div class='col-lg'>
                    {{ Form::model($point, ['url' => route('points.update', [$organization, $point]), 'method' => 'patch', 'class' => 'form-group']) }}
                    @include('organizations.points._form', [
                        'displayInheritanceSchedule' => true
                    ])
                    @if(Auth::user()->canUpdatePoints())
                        <div class='row justify-content-center'>
                            <div class='col-lg-6 text-right'>
                                <div class="btn-group">
                                    <button type='submit' class="btn btn-success" onclick='return confirm("Обновить точку?")'>
                                        Сохранить
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
