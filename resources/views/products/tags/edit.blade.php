<?php
/**
 * @var \App\Models\Organizations\Organization $organization
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='tag'>
            <div class='row ml-0 justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($tag, ['url' => route('tags.update', $tag), 'method' => 'patch', 'class' => 'form-group']) }}
                    @include('products.tags._form')
                    @if (Auth::user()->canUpdateTags())
                        <div class='row mx-0 mb-3 justify-content-end'>
                            <div class="btn-group">
                                <button type='submit' class="btn btn-success" onclick='return confirm("Обновить тег?")'>
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    @endif
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
