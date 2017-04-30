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
    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">--}}
    @yield('head')
    @yield('css')
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{config('app.name')}}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                @if (Auth::check())
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/project') }}">Projects</a></li>

                    @can('read', 'std-activity')
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Standard Activity <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('activity-division.index')}}">Divisions</a></li>
                            <li><a href="{{route('std-activity.index')}}">Standard Activities</a></li>
                            <li><a href="{{route('breakdown-template.index')}}">Breakdown Templates</a></li>
                        </ul>
                    </li>
                    @endcan
                    @can('read', 'resources')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Resources <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('resource-type.index')}}">Resource Types</a></li>
                            <li><a href="{{route('resources.index')}}">Resources</a></li>
                            <li><a href="{{route('business-partner.index')}}">Business Partners</a></li>
                            <li><a href="{{route('unit.index')}}">Units of measure</a></li>
                        </ul>
                    </li>
                    @endcan

                    @can('read', 'productivity')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle btnhover"  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Productivity<span class="caret"></span></a>
                        <ul class="dropdown-menu ">
                            <li><a href="{{route('csi-category.index')}}">CSI Category</a></li>
                            <li><a href="{{route('productivity.index')}}">Productivity</a></li>

                        </ul>
                    {{--<li><a href="{{route('productivity.report')}}">Reports</a></li>--}}

                    </li>
                    @endcan

                    @if (Auth::user()->is_admin)
                        <li><a href="{{route('users.index')}}">Users</a></li>
                    @endif

                </ul>

                @endif

                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::check())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fa fa-user"></i> {{ Auth::user()->name }} <span class="caret "></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="panel panel-default" id="main-panel">
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
    </div>

    <!-- JavaScripts -->
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>--}}

    <script src="{{asset('/js/bootstrap.js')}}"></script>
    {{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>--}}
    @yield('javascript')

    @if (env('APP_ENV') != 'local')
    <script>

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-90058309-1', 'auto');
        ga('send', 'pageview');
    </script>
    @endif
</body>
</html>
