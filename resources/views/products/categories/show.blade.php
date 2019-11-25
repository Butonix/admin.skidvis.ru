<?php
/**
 * @var \App\Models\Products\Category $category
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container pb-lg-5 px-0">
        <div class='category'>
            @if(Auth::user()->canUpdateCategories())
                <div class='row mx-0 mb-3 justify-content-end'>
                    <div class="btn-group">
                        <a role='button' class="btn btn-outline-primary" href='{{ route('categories.edit', $category) }}'>
                            Редактировать
                        </a>
                    </div>
                </div>
            @endif
            <div class='row justify-content-center'>
                <div class='col-lg-6'>
                    {{ Form::model($category, ['class' => 'form-group']) }}
                    <div class='row ml-0'>
                        <div class='col-lg-3 mr-3'>
                            <div class='row categories-images {{ ($category->forProducts()) ? 'active' : '' }}'>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-file'>
                                            <div class='icon-file__wrapper {{ (is_null($category->getEmptyImageLink())) ? 'icon-file__wrapper--empty' : '' }}'
                                                 style='background-image: url({{ $category->getEmptyImageLink() }});'></div>
                                        </div>
                                    </div>
                                    <div class='row justify-content-center'>
                                        <div class='col-lg-12 text-center'>
                                            <small class='text-muted category__icon--svg'>
                                                Пустая иконка
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-file'>
                                            <div class='icon-file__wrapper {{ (is_null($category->getImageLink())) ? 'icon-file__wrapper--empty' : '' }}'
                                                 style='background-image: url({{ $category->getImageLink() }});'></div>
                                        </div>
                                    </div>
                                    <div class='row justify-content-center'>
                                        <div class='col-lg-12 text-center'>
                                            <small class='text-muted category__icon--svg'>
                                                Обычная иконка
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-file'>
                                            <div class='icon-file__wrapper {{ (is_null($category->getActiveImageLink())) ? 'icon-file__wrapper--empty' : '' }}'
                                                 style='background-image: url({{ $category->getActiveImageLink() }});'></div>
                                        </div>
                                    </div>
                                    <div class='row justify-content-center'>
                                        <div class='col-lg-12 text-center'>
                                            <small class='text-muted category__icon--svg'>
                                                Активная иконка
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-file'>
                                            <div class='icon-file__wrapper {{ (is_null($category->getBusinessImageLink())) ? 'icon-file__wrapper--empty' : '' }}'
                                                 style='background-image: url({{ $category->getBusinessImageLink() }});'></div>
                                        </div>
                                    </div>
                                    <div class='row justify-content-center'>
                                        <div class='col-lg-12 text-center'>
                                            <small class='text-muted category__icon--svg'>
                                                Бизнес-иконка, обычная
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-lg-12'>
                                    <div class='row justify-content-center'>
                                        <div class='icon-file'>
                                            <div class='icon-file__wrapper {{ (is_null($category->getBusinessActiveImageLink())) ? 'icon-file__wrapper--empty' : '' }}'
                                                 style='background-image: url({{ $category->getBusinessActiveImageLink() }});'></div>
                                        </div>
                                    </div>
                                    <div class='row justify-content-center'>
                                        <div class='col-lg-12 text-center'>
                                            <small class='text-muted category__icon--svg'>
                                                Бизнес-иконка, активная
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg'>
                            @include('products.categories._form', [
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
