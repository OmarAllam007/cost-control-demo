@if(request('paint'))
    @include('layouts.buttons')
@endif
<p style="page-break-before:always;"></p>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="{{asset('/css/print.css')}}">
{{--    <link rel="stylesheet" href="{{asset('/css/app.css')}}">--}}
    @yield('css')
    @yield('style')
</head>


<body id="app-layout">

<table>
    <thead>
    <tr>
        <th>
            <strong>
                AlKifah Contracting Co. <br>
                Project Control Department <br>
                Project: {{$project->name}} <br>
                {{date('d M Y')}}
            </strong>
        </th>
        <th class="header-text text-center">
            @yield('header')
        </th>
        <th>
            <img src="{{asset('/images/kcc.png')}}" alt="Logo" class="logo pull-right" width="200">
        </th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td colspan="3">
            @yield('body')
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
<p style="page-break-after:always;"></p>
