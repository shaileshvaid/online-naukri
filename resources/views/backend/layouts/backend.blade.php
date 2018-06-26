{{--
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content=""/>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1"/>
        <meta name="msapplication-tap-highlight" content="no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="Milestone">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Milestone">
        <meta name="theme-color" content="#4C7FF0">

        <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
        
        <meta name="_token" content="{{ csrf_token() }}">
        <title>@yield('site_title',@$configuration['site_title'])</title>
       
        <link rel="stylesheet" href="{{ elixit('assets/css/app.css') }}"/>
        <link rel="stylesheet" href="{{ elixit('assets/css/common.css') }}"/>


        @if(isset($configuration['enable_rtl_support']) && $configuration['enable_rtl_support'] == 'yes')
            <link rel="stylesheet" href="{{ elixit('assets/css/rtl/app.css') }}"/>
            <link rel="stylesheet" href="{{ elixit('assets/css/rtl/common.css') }}"/>
        @endif

        @yield('stylesheets')
    </head>
    <body ng-app="BackendApp">
        <div class="app skin-4 fixed-header horizontal">
            <!-- content panel -->
            <div class="main-panel">
                <!-- top header -->
                @include('backend.layouts.common.top-header')
                <!-- /top header -->
                <!-- menu header -->
                @include('backend.layouts.common.menu-header')
                <!-- /menu header -->
                <!-- main area -->
                <div class="main-content">
                    <div class="content-view">
                        @yield('content')
                    </div>
                    <!-- bottom footer -->
                    @include('backend.layouts.common.footer')
                    <!-- /bottom footer -->
                </div>
                <!-- /main area -->
            </div>
            <!-- /content panel -->
        </div>
        @include('backend.layouts.common.vars')

        <script type="text/javascript" src="{{ elixit('assets/js/app.js') }}"></script>

        <script type="text/javascript" src="{{ elixit('assets/js/tinymce/tinymce.min.js') }}"></script>
        <script type="text/javascript" src="{{ elixit('assets/js/angular-tinymce.js') }}"></script>
        
        <script type="text/javascript" src="{{ elixit('assets/js/helper.js') }}"></script>
        <script type="text/javascript" src="{{ elixit('assets/js/angular-modules.js') }}"></script>
        <script type="text/javascript" src="{{ elixit('assets/js/backend/app.js') }}"></script>

        @yield('scripts')
    </body>
</html>
--}}
This is home page
