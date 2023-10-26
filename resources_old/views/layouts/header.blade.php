<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

<!--  <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-free-6.2.0-web/css/fontawesome.css') }}">
    <script>
        WebFont.load({
            custom: {"families":["Flaticon", "Font Awesome 6 Solid", "Font Awesome 6 Regular", "Font Awesome 6 Brands", "simple-line-icons"], urls: ['{{ asset('assets/css/fonts.min.css') }}']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!--
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-free-5.8.2-web/css/fontawesome.css') }}">
    <script>
        WebFont.load({
            custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset('assets/css/fonts.min.css') }}']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
<!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/atlantis-min.css') }}">
</head>
