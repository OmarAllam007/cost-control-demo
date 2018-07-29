<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">

    <title>
        @hasSection('title')
            @yield('title') |
        @endif
        {{config('app.name')}} &mdash; {{ title_case(app()->environment()) }}
    </title>
    <style>
        html,body {
            min-height: 100%;
        }
    </style>
    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">--}}

    @yield('head')
    @yield('css')
</head>
<body id="app-layout">
<div id="app">
    <div class="container-fluid">
        <div class="display-flex">
            <div class="logo-line">
                <a href="/">
                    <img src="{{asset('images/kps-logo.png')}}" alt="" width="125">
                </a>
            </div>

            <div class="user-area">
                <strong>

                    {{auth()->user()->name}},

                    <a href="{{url('/logout')}}">Logout</a>
                </strong>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="panel panel-default flex" id="main-panel" >
            <div class="panel-heading clearfix">
                @yield('header')
            </div>

            <div class="panel-body">
                @if (session()->has('flash-message'))
                    @include("partials/alert/" . session('flash-type'), ['message' => session('flash-message')])
                @endif

                @yield('body')    
            </div>
        </div>

        <footer class="container-fluid">
            <div class="footer display-flex">
                <div class="text-danger">Copyright &copy; AlKifah Contracting {{date('Y')}}</div>
                <img src="{{asset('images/kcc-logo.png')}}" alt="" height="50">
            </div>
        </footer>
    </div>
</div>


    <script src="{{asset('/js/bootstrap.js')}}"></script>
    @yield('javascript')
</body>
</html>
