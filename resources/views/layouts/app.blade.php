<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('app.name')}}</title>

    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
    <link rel="stylesheet" href="{{asset('/css/font-awesome-4.6.2/')}}">
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('') }}">
                    {{config('app.name')}}

                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                @if (Auth::check())
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/project') }}" class="fa fa-tasks"> Projects</a></li>

                    <li><a href="{{ url('/unit') }}" class="fa fa-star-half-empty"> Unit</a></li>

                    <li class="dropdown">
                    <li><a href="{{ url('/survey') }}" class="fa fa-file-text-o "> Survey</a></li>

                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle fa fa-cogs" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" > Resources <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('resource-type.index')}} " >Resources Type</a></li>
                            <li><a href="{{route('resources.index')}}">Resources</a></li>
                            <li><a href="{{route('business-partner.index')}}">Business Partner</a></li>
                        </ul>
                    </li>
                    <li><a href="{{route('productivity.index')}}" class="fa fa-area-chart"> Productivity</a></li>

                </ul>
                @endif

                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Register</a></li>

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

    <div class="container">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    @yield('javascript')
</body>
</html>
