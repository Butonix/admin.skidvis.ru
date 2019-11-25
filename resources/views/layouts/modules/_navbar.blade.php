<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto text-right">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Вход</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @endif
                @else
                    @if(URL::current() !== route('home'))
                        @if(Auth::user()->isSuperAdministrator())
                            <li class="nav-item dropdown">
                                <a id="adminka-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Пользователи <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="adminka-dropdown">
                                    <a class="dropdown-item text-lg-left text-right" href="{{ route('auth-providers.index') }}">Сервисы
                                        авторизации</a>
                                    <a class="dropdown-item text-lg-left text-right" href="{{ route('users.index') }}">Пользователи</a>
                                    <a class="dropdown-item text-lg-left text-right" href="{{ route('roles.index') }}">Роли</a>
                                    <a class="dropdown-item text-lg-left text-right" href="{{ route('permissions.index') }}">Разрешения</a>
                                </div>
                            </li>
                        @endif

                        @if(Auth::user()->isSuperAdministrator() || Auth::user()->isAdministrator() || Auth::user()->isManager())
                            {{--<li class="nav-item dropdown">--}}
                            {{--<a id="adminka-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>--}}
                            {{--Организации <span class="caret"></span>--}}
                            {{--</a>--}}
                            {{--<div class="dropdown-menu dropdown-menu-right" aria-labelledby="adminka-dropdown">--}}
                            {{--<a class="dropdown-item text-lg-left text-right" href="{{ route('organizations.index') }}">Организации</a>--}}

                            {{--@if(Auth::user()->isAdministrator())--}}
                            {{--<a class="dropdown-item text-lg-left text-right" href="#">Адреса</a>--}}
                            {{--@endif--}}

                            {{--<a class="dropdown-item text-lg-left text-right" href="{{ route('products.all') }}">Все--}}
                            {{--акции</a>--}}
                            {{--</div>--}}
                            {{--</li>--}}
                        @endif

                        @if(Auth::user()->isSuperAdministrator() || Auth::user()->isModerator() || Auth::user()->canWorkWithTags() || Auth::user()->canWorkWithCategories() || Auth::user()->canWorkWithArticles())
                            <li class="nav-item dropdown">
                                <a id="adminka-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Разное <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="adminka-dropdown">
                                    @if(Auth::user()->canWorkWithArticles())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('articles.index') }}">Статьи</a>
                                    @endif

                                    @if(Auth::user()->isSuperAdministrator())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('article-labels.index') }}">Лейблы
                                            для статей</a>
                                    @endif

                                    @if(Auth::user()->canWorkWithCategories())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('categories.index') }}">Категории</a>
                                    @endif

                                    @if(Auth::user()->canWorkWithTags())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('tags.index') }}">Теги</a>
                                    @endif

                                    @if(Auth::user()->isSuperAdministrator())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('social-networks.index') }}">Соц.сети</a>
                                    @endif

                                    @if(Auth::user()->canWorkWithAuditories())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('auditories.index') }}">Аудитория</a>
                                    @endif

                                    @if(Auth::user()->canWorkWithHolidays())
                                        <a class="dropdown-item text-lg-left text-right" href="{{ route('holidays.index') }}">Праздники / выходные</a>
                                    @endif
                                </div>
                            </li>
                        @endif
                    @endif

                    @if(Auth::user()->isSuperAdministrator())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell"></i>
                                <span class="badge {{ (Auth::user()->getUnreadNotificationsCount() > 0) ? 'badge-danger' : 'badge-secondary' }}">
                                    {{ Auth::user()->getUnreadNotificationsCount() }}
                                </span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->getName()}} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item text-lg-left text-right" href="{{ route('cabinet.edit') }}">Мой
                                профиль</a>
                            <a class="dropdown-item text-lg-left text-right" href="{{ route('cabinet.update.password.view') }}">Сменить
                                пароль</a>
                            <a class="dropdown-item text-lg-left text-right" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                Выход
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
