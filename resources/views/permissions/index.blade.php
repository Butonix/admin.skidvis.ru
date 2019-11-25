@extends('layouts.app')

@section('content')
    @include('permissions.components._index')
    @include('permissions.components.createModal')
@stop
