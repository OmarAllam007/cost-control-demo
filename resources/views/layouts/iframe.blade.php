<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">

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
