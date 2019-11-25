<?php
/**
 * @var \App\Models\Articles\ArticleLabel $label
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='category'>
            <div class='row mx-0 mb-3 justify-content-end'>
                <div class="btn-group">
                    <a role='button' class="btn btn-outline-primary" href='{{ route('article-labels.edit', $label) }}'>
                        Редактировать
                    </a>
                </div>
            </div>
            <div class='row justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($label, ['class' => 'form-group']) }}
                    <div class='row ml-0'>
                        <div class='col-lg-3 mr-3'>
                            <div class='row'>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-label-file'>
                                            <div class='icon-label-file__wrapper {{ (is_null($label->getImageLink())) ? 'icon-label-file__wrapper--empty' : '' }}'
                                                 style='background-image: url({{ $label->getImageLink() }});'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg'>
                            @include('articles.labels._form', [
                                'readonly' => true
                            ])
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
