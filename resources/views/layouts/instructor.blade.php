<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ get_language_direction() }}">

<head>

    {{ config(['app.name' => get_settings('system_title')]) }}
    <title>@stack('title') | {{ config('app.name') }}</title>

    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link rel="shortcut icon" href="{{ asset(get_frontend_settings('favicon')) }}" />
    <meta content="{{ csrf_token() }}" name="csrf_token" />
    @stack('meta')
    <!-- End meta -->

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/backend/vendors/bootstrap/bootstrap.min.css') }}" />

    {{-- FlatIcons --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/icons/uicons-solid-rounded/css/uicons-solid-rounded.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/icons/uicons-bold-rounded/css/uicons-bold-rounded.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/icons/uicons-regular-rounded/css/uicons-regular-rounded.css') }}" />

    {{-- Font awesome icons --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/icon-picker/icons/fontawesome-all.min.css') }}" />

    {{-- Select2 --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/select2/select2.min.css') }}" />

    {{-- Custom css --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/backend/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/backend/css/responsive.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/backend/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/backend/css/admin-modern.css') }}">

    @stack('css')

    <script type="text/javascript" src="{{ asset('assets/backend/js/jquery-3.7.1.min.js') }}"></script>
</head>

<body>
    <main>
        <!-- Sidebar Navigation -->
        <div class="ol-sidebar">
            @include('instructor.navigation')
        </div>

        <div class="ol-sidebar-content">
            @include('instructor.header')
            <div class="ol-body-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>


    @include('instructor.modal')

    <script src="{{ asset('assets/backend/vendors/bootstrap/bootstrap.bundle.min.js') }}"></script>

    {{-- Jquery form --}}
    <script type="text/javascript" src="{{ asset('assets/global/jquery-form/jquery.form.min.js') }}"></script>

    {{-- Jquery UI --}}
    <script type="text/javascript" src="{{ asset('assets/global/jquery-ui-1.13.2/jquery-ui.min.js') }}"></script>

    {{-- Select2 --}}
    <script src="{{ asset('assets/global/select2/select2.min.js') }}"></script>

    <script src="{{ asset('assets/backend/js/script.js') }}"></script>
    <script src="{{ asset('assets/backend/js/custom.js') }}"></script>


    @include('instructor.toaster')
    @include('instructor.common_scripts')
    @include('instructor.init')
    @stack('js')
</body>

</html>
