<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
</head>
<body id="iframe">

<div class="container-fluid">
    @if (session()->has('flash-message'))
        @include("partials/alert/" . session('flash-type'), ['message' => session('flash-message')])
    @endif

    @yield('body')
</div>

<script src="{{asset('/js/bootstrap.js')}}"></script>

@yield('javascript')

@if (\Request::has('close'))
    <script>
        if (window.parent != window) {
            setTimeout(function(){
                window.parent.location.reload();
            }, 3000)
        }
    </script>
@endif
</body>
</html>
