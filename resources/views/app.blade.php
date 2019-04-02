<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta id="csrf" name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous" defer>
    

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Turbo Links -->
    <meta name="turbolinks-cache-control" content="no-cache">
</head>
<body>
    @php
        if (Auth::user()){
            $data['user'] = Auth::user()->only('id', 'email', 'username');
        } else {
            $data['user'] = null;
        }

        $data['csrf'] = csrf_token();
    @endphp
    <div id="app" data-component="{{ $name }}" data-props="{{ json_encode($data) }}"></div>
</body>
</html>