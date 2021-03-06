@php
    $page = $page ?? '';
@endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
    <a class="navbar-brand" href="/">MoneyInOut</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item @if (route::is('cms.index')) active @endif">
                <a class="nav-link" href="{{ route('cms.index') }}">Панель управления<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item @if (route::is('cms.categories.index')) active @endif">
                <a class="nav-link" href="{{ route('cms.categories.index') }}">Категории<span class="sr-only">(current)</span></a>
            </li>
            @if (Auth::user()->level == \App\Models\User::LEVEL_ADMIN)
                <li class="nav-item @if (route::is('cms.users.index')) active @endif">
                    <a class="nav-link" href="{{ route('cms.users.index') }}">Пользователи<span class="sr-only">(current)</span></a>
                </li>
            @endif
        </ul>
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            Настройки
                        </a>

                        <a class="dropdown-item" href="{{ route('logout') }}"
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

</nav>
<nav class="navbar navbar-light bg-light">

</nav>
