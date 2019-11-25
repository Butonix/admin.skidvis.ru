@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class='col-lg-12'>
                {{-- Блок для работы с пользователями --}}
                @if(Auth::user()->isSuperAdministrator())
                    <div class='row management'>
                        <div class='col-lg-12'>
                            <h4 class='mb-3'>Пользователи</h4>
                            <div class='row'>
                                <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                    <a href='{{ route('auth-providers.index') }}'>
                                        <div class='management-card__wrapper'>
                                            <div class='management-card__body'>
                                                <div class='management-card__body__text'>
                                                    Сервисы авторизации
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                    <a href='{{ route('users.index') }}'>
                                        <div class='management-card__wrapper'>
                                            <div class='management-card__body'>
                                                <div class='management-card__body__text'>
                                                    Пользователи
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                    <a href='{{ route('roles.index') }}'>
                                        <div class='management-card__wrapper'>
                                            <div class='management-card__body'>
                                                <div class='management-card__body__text'>
                                                    Роли
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                    <a href='{{ route('permissions.index') }}'>
                                        <div class='management-card__wrapper'>
                                            <div class='management-card__body'>
                                                <div class='management-card__body__text'>
                                                    Разрешения
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Блок для всего остального --}}
                @if(Auth::user()->isSuperAdministrator() || Auth::user()->isModerator() || Auth::user()->canWorkWithTags() || Auth::user()->canWorkWithCategories() || Auth::user()->canWorkWithArticles())
                    <div class='row management'>
                        <div class='col-lg-12'>
                            <h4 class='mb-3'>Разное</h4>
                            <div class='row'>
                                @if(Auth::user()->canWorkWithArticles())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('articles.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Статьи
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if(Auth::user()->isSuperAdministrator())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('article-labels.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Лейблы для статей
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if(Auth::user()->canWorkWithCategories())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('categories.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Категории
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if(Auth::user()->canWorkWithTags())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('tags.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Теги
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if(Auth::user()->isSuperAdministrator())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('social-networks.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Соц.сети
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if(Auth::user()->canWorkWithAuditories())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('auditories.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Аудитория
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                                @if(Auth::user()->canWorkWithHolidays())
                                    <div class='management-card col-lg-3 col-md-4 col-sm-6 col-6'>
                                        <a href='{{ route('holidays.index') }}'>
                                            <div class='management-card__wrapper'>
                                                <div class='management-card__body'>
                                                    <div class='management-card__body__text'>
                                                        Праздники / выходные
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
