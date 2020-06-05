<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - Watchlist</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href=/css/app.css?{{ date('YmdHis') }}">
    @yield('styles')
</head>
<body>

@include('blocks.navbar.index')

<div class="container" id="app">
    @yield('content')
</div>

{{--@include('blocks.footer.index')--}}
@stack('scripts')
<script src="/js/app.js?{{ date('YmdHis') }}"></script>

</body>
</html>
