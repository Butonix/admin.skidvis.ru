<?php
/**
 * @var \App\Models\Products\Category $category
 */
?>

@extends('layouts.app')

@section('content')
    @include('products.categories.filters._filters', [
        'frd' => $frd
    ])
    <div class="container px-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class='row no-gutters'>
                    <div class="col-lg mb-lg-2 mt-lg-2 col-md mb-md-2 mt-md-2 col-sm mb-sm-2 mt-sm-2 col mb-2 mt-2">
                        <div class='row justify-content-end'>
                            <div class="col-lg-auto col-md-auto col-sm-auto col-auto pr-lg-2 pr-1">
                                <button type='button' class="btn btn-secondary" style='padding-top: .7rem; padding-bottom: .7rem;'
                                        data-tooltip='true' data-placement='bottom' title='Фильтры' id='find-fields-open-button'>
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                            @if(Auth::user()->canCreateCategories())
                                <div class="col-lg-auto col-md-auto col-sm-auto col-auto pl-lg-2 pl-1">
                                    <a href='{{ route('categories.create') }}' role='button' class="btn btn-success" style='padding-top: .475rem;'
                                       data-tooltip='true' data-placement='bottom' title='Новая категория'>
                                        Добавить <i class="fab fa-wpforms"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @include('products.categories._categories')
            </div>
        </div>
    </div>
    @include('forms._pagination', [
        'elements' => $categories,
        'frd' => $frd
    ])
@stop
