<!DOCTYPE html>
<html>

    <head>

        {{-- Base Meta Tags --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
        <title>LGU-PAGADIAN ONLINE RPT PAYMENT</title>
    </head>

    <style>

        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;1,300&display=swap');

        body {
            background: #ecf0f1;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            margin-bottom: 55px;
        }

        .card-header .project-title {
            font-weight: bold;
        }

        .card-header .username {
            font-size: 15px;
        }

    </style>


<body>

    <script src="{{ asset('js/app.js') }}" defer></script>

    @yield('header')

    <div id="content" class="container p-2">
        @yield('content')
    </div>

    @yield('footer')



</body>

</html>
