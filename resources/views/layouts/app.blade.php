<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('app.name')}}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
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
                {{--@if (Auth::check())--}}
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/project') }}">Projects</a></li>
                    {{--<li><a href="{{route('category.index')}}">Quantity Survey Categories</a></li>--}}
                    {{--<li>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="{{url('/survey')}}">Quantity Survey <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('survey.index')}}">Quantity Survey</a></li>
                        </ul>
                    </li>--}}
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Standard Activity <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('activity-division.index')}}">Divisions</a></li>
                            <li><a href="{{route('std-activity.index')}}">Standard Activities</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Resources <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('resource-type.index')}}">Resource Types</a></li>
                            <li><a href="{{route('resources.index')}}">Resources</a></li>
                            <li><a href="{{route('business-partner.index')}}">Business Partners</a></li>
                            <li><a href="{{route('unit.index')}}">Units of measure</a></li>
                        </ul>
                    </li>
                    <li><a href="{{route('productivity.index')}}">Productivity</a></li>

                </ul>
                {{--@endif--}}

                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::guest())
                    {{-- --}}

                    @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle fa fa-user" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret "></span>
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
                @yield('body')    
            </div>
        </div>
    </div>
    
    <!-- JavaScripts -->
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>--}}

    <script src="{{asset('/js/bootstrap.js')}}"></script>

    @yield('javascript')
</body>
</html>
