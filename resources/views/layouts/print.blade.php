<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('/css/print.css')}}">
{{--    <link rel="stylesheet" href="{{asset('/css/app.css')}}">--}}
    @yield('css')
    @yield('style')
</head>


<body id="app-layout">

<table style="width:100%">
    <thead>
    <tr>
        <th class="col-xs-4">
            <strong>
                AlKifah Contracting Co. <br>
                Project Control Department <br>
                @if (isset($project))
                Project: {{$project->name}} <br>
                @endif
                {{date('d M Y')}}
            </strong>
        </th>
        <th class="header-text text-center col-xs-4">
            @yield('header')
        </th>
        <th class="col-xs-4">
            <img src="{{asset('/images/kcc.png')}}" alt="Logo" class="logo pull-right" width="200">
        </th>
    </tr>
    <tr>
        <th colspan="3" style="height: 1cm;"></th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td colspan="3" style="max-width: 800px; min-width: 700px;">
            <div class="container">
                @yield('body')
            </div>
        </td>
    </tr>
    </tbody>
</table>

<script src="{{asset('/js/bootstrap.js')}}"></script>

@yield('javascript')


@if (env('APP_ENV') != 'local')
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-90058309-1', 'auto');
        ga('send', 'pageview');
    </script>
@endif

</body>
</html>