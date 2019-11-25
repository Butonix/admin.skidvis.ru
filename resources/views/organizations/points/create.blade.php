@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='row justify-content-center'>
            <div class='col-lg'>
                {{ Form::open(['url' => route('points.store', $organization), 'method' => 'post', 'class' => 'form-group', 'enctype' => 'multipart/form-data']) }}
                @include('organizations.points._form', [
                    'displayInheritanceSchedule' => true
                ])
                @if(Auth::user()->canCreatePoints())
                    <div class="row justify-content-center">
                        <div class='col-lg-6 text-right'>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary" onclick='return confirm("Создать точку?")'>
                                    Создать
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
