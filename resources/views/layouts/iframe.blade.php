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
    @yield('body')
</div>

<script src="{{asset('/js/bootstrap.js')}}"></script>

@yield('javascript')

@if (\Request::has('reload'))
    <script>
        if (window.parent != window) {
            window.parent.app.reload('{{request('reload')}}', {
                message: '{{session('flash-message')}}',
                type: '{{session('flash-type')}}'
            });
        }
    </script>
@endif
</body>
</html>
