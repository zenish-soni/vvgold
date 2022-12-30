<!DOCTYPE html>
<html lang="en" class="light-style">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{csrf_token()}}" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
        <title>@yield('title') - {{env('APP_NAME')}}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{asset('favicon.ico')}}" />
        <link rel="shortcut icon" type="image/x-icon" href="{{asset('favicon.ico')}}" />
        <link rel="apple-touch-icon" href="{{asset('favicon.ico')}}" />

         <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

        <!-- Icons -->
        <link rel="stylesheet" href="{{asset('public/vendor/boxicons/css/boxicons.css')}}" />
        <link rel="stylesheet" href="{{asset('public/vendor/fontawesome/css/all.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/vendor/perfect-scrollbar/perfect-scrollbar.css')}}" />
        <link rel="stylesheet" href="{{asset('public/vendor/toastr/toastr.css')}}" />
        <link rel="stylesheet" href="{{asset('public/css/core.css')}}" />
        <link rel="stylesheet" href="{{asset('public/css/theme-default.css')}}" />
        <link rel="stylesheet" href="{{asset('public/css/pages/page-auth.css')}}" />
        <link rel="stylesheet" href="{{asset('public/vendor/select2/css/select2.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/css/design.css?'.time())}}" />
        @yield('style')
    </head>

    <body>
        <div class="page-loader-wrapper" style="display: none;">
            <div class="loader">
                <img class="loading-img-spin" src="{{asset('public/img/logo.png')}}" width="20" height="20" alt="admin" />
            </div>
        </div>

        <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
            <div class="layout-container">

                @include('user/includes/header')
                <div class="layout-page">
                    <div class="content-wrapper">
                        @include('user/includes/navigation')
                        <div class="container-fluid flex-grow-1 container-p-y">
                            @yield('content')
                        </div>

                        <div class="content-backdrop fade"></div>

                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div> -->

        @yield('popup')

        <script src="{{asset('public/vendor/jquery.js')}}"></script>
        <script src="{{asset('public/vendor/popper.min.js')}}"></script>
        <script src="{{asset('public/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('public/vendor/sweetalert2/sweetalert2.min.js')}}"></script>
        <script src="{{asset('public/vendor/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
        <script src="{{asset('public/vendor/toastr/toastr.js')}}"></script>
        <script src="{{asset('public/js/config.js')}}"></script>
        <script src="{{asset('public/js/helpers.js')}}"></script>
        <script src="{{asset('public/js/menu.js')}}"></script>
        <script src="{{asset('public/js/main.js')}}"></script>

        <script src="{{asset('public/vendor/knockout/knockout.js')}}"></script>
        <script src="{{asset('public/vendor/knockout/knockout.mapping.js')}}"></script>
        <script src="{{asset('public/vendor/knockout/knockout.validation.js')}}"></script>
        <script src="{{asset('public/js/common.js')}}"></script>
        <script src="{{asset('public/js/pager.js')}}"></script>

        @yield('script')
    </body>
</html>
