<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $companyName }}</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__ttl">
                <a href="/" class="header__logo">{{ $companyName }}</a>
            </div>
            <div class="header__nav">
                <ul class="header__nav--ul">
                    @section('header-nav')
                    @show
                </ul>
            </div>
        </div>
    </header>
    <main class="main">
        <div class="content__date" id="date"></div>
        <div class="content__time" id="time"></div>
        @yield('content')
        <script src="{{ asset('js/clock.js') }}"></script>
    </main>
</body>
</html>
