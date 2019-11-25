<?php
/**
 * @var \App\Models\Products\Tag $tag
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='tag'>
            @if (Auth::user()->canUpdateTags())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('tags.edit', $tag) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($tag, ['class' => 'form-group']) }}
                    @include('products.tags._form', [
                        'readonly' => true,
                    ])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
