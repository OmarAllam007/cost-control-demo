@include('layouts.cover')
<p style="page-break-before:always;"></p>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" href="{{asset('/css/print.css')}}">
</head>


<body id="app-layout">

<table class="table">
    <thead>
    <tr>
        <th width="33%">
            <strong>
                AlKifah Contracting Co. <br>
                Project Control Department <br>
                Budget Team <br>
                Project: {{$project->name}} <br>
                {{date('d M Y')}}
            </strong>
        </th>
        <th width="34%" class="header-text text-center">
            @yield('header')
        </th>
        <th width="33%">
            <img src="{{asset('/images/kcc.png')}}" alt="Logo" class="logo pull-right">
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


{{--<div class="panel panel-default" id="main-panel">
    <div class="panel-heading clearfix">

    </div>

    <div class="panel-body">
        @if (session()->has('flash-message'))
            @include("partials/alert/" . session('flash-type'), ['message' => session('flash-message')])
        @endif


    </div>
</div>--}}


<script src="{{asset('/js/bootstrap.js')}}"></script>

@yield('javascript')

<script>
    window.print();
</script>
</body>
</html>
<p style="page-break-before:always;"></p>
{{--<p style="page-break-after:always;"></p>--}}