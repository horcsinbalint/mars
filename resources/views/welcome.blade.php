<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ config('app.logo_with_bg_path') }}">

    <title>{{ config('app.name', 'Urán') }}</title>

    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}"/>
    <link rel="stylesheet" href="{{ mix('css/welcome_page.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ mix('css/materialize.css') }}" media="screen,projection" />

    @if (config('app.debug'))
        <!-- Scripts -->
        <script type="text/javascript" src="{{ mix('js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/materialize.js') }}"></script>
    @endif
</head>

<body class="{{ Cookie::get('theme') }}">
    @if (Route::has('login'))
    <header>
        <div class="navbar-fixed">
            <nav class="top-nav">
                <div class="nav-wrapper">
                    <div class="row">
                        <ul class="right">
                            @auth
                                <li><a href="{{ url('/home') }}">@lang('general.home')</a></li>
                                @else
                                <li><a href="{{ route('login') }}">@lang('general.login')</a></li>
                                @if(\App\Models\Feature::isFeatureEnabled("application"))
                                    <li><a href="{{ route('register') }}">@lang('general.register_collegist')</a></li>
                                @endif
                                @if(\App\Models\Feature::isFeatureEnabled("guests"))
                                        <li><a href="{{ route('register.guest') }}">@lang('general.register_guest')</a></li>
                                @endif
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    @endif
    </header>
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="row">
                <div class="col s12 l2 offset-l2 center-align">
                    <img class="material-icons @if(config('app.debug'))tooltipped" data-tooltip='<div>Icons made by monkik from www.flaticon.com</div>'@else"@endif style="height:100px" src="{{ config('app.logo_blue_path') }}">
                </div>
                <div class="col s12 l5 center-align">
                    <div class="noselect"
                        style="text-indent:15px;font-size:80px;text-transform: uppercase;font-weight:300;letter-spacing:3px;margin-left:20px">
                        {{ config('app.name') }} </div>
                </div>
            </div>

            <div class="links">
                <a href="https://eotvos.elte.hu">@lang('general.mars_descr')</a>
            </div>
        </div>
    </div>

    <div class="links center-align">
        <a href="#">
            @lang('main.better')</a><br class="mobile-break" />
        <a href="#">
            @lang('main.faster')</a><br class="mobile-break" />
        <a href="#">
            @lang('main.brilliant')</a><br class="mobile-break" />
        <a href="#">
            @lang('main.essential')</a><br class="mobile-break" />
        <a href="#">
            @lang('main.modern')</a><br class="mobile-break" />
        <a href="https://github.com/EotvosCollegium/mars">
            @lang('main.open')</a><br class="mobile-break" />
    </div>

    @if (config('app.debug'))
        <script>
            $(document).ready(function(){
                $('.tooltipped').tooltip();
            });
        </script>
    @endif
</body>

</html>
