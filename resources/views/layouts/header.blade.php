<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

<!--  <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-free-6.2.0-web/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.css') }}">
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
    <style>
        .table-small-padding tr th, .table-small-padding tr  td {
            padding-left: 25px !important;
            padding-right: 25px !important;
            font-size: 14px !important;
        }
        @media (max-width: 1880px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 15px !important;
                padding-right: 15px !important;
                font-size: 14px !important;
            }
        }
        @media (max-width: 1780px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 12px !important;
                padding-right: 12px !important;
                font-size: 14px !important;
            }
        }
        @media (max-width: 1680px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 10px !important;
                padding-right: 10px !important;
                font-size: 14px !important;
            }
        }
        @media (max-width: 1580px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 10px !important;
                padding-right: 10px !important;
                font-size: 14px !important;
            }
        }
        @media (max-width: 1480px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 10px !important;
                padding-right: 10px !important;
                font-size: 13px !important;
            }
        }
        @media (max-width: 1380px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 9px !important;
                padding-right: 9px !important;
                font-size: 12px !important;
            }
        }
        @media (max-width: 1280px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 9px !important;
                padding-right: 9px !important;
                font-size: 11px !important;
            }
        }
        @media (max-width: 1250px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 9px !important;
                padding-right: 9px !important;
                font-size: 10px !important;
            }
        }
        @media (max-width: 1200px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 9px !important;
                padding-right: 9px !important;
                font-size: 9px !important;
            }
        }
        @media (max-width: 1150px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 9px !important;
                padding-right: 9px !important;
                font-size: 9px !important;
            }
        }
        @media (max-width: 1100px) {
            .table-small-padding tr th, .table-small-padding tr  td {
                padding-left: 9px !important;
                padding-right: 9px !important;
                font-size: 8px !important;
            }
        }
    </style>
</head>
