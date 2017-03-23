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
</head>


<body id="app-layout">

<table>
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
<input type="hidden" value="{{$project->id}}" id="project_id">

<script src="{{asset('/js/bootstrap.js')}}"></script>

@yield('javascript')

<script>
    var global_selector = 0;
    function changeButtonBackroundColor() {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        $('.' + hash[1]).css('background', '#3e5a20');
    }

    $(function () {
        var project_id = $('#project_id').val();

        //negative_var
        var negative_variance = sessionStorage.getItem('negative_var_' + project_id);
        var activity = sessionStorage.getItem('activity_' + project_id);
        var wbs = sessionStorage.getItem('wbs_' + project_id);
        var productivity = sessionStorage.getItem('budget_productivity_' + project_id);

        var type = sessionStorage.getItem('dictionary_' + project_id);
        console.log(type , sessionStorage)

        if (negative_variance != 0 && negative_variance != null) {
            console.log('variance', negative_variance)
            var articles = $('.negative_var');
            articles.each(function () {
                $(this).parents().each(function () {
                    $(this).addClass('in').removeClass('hidden')
                });
                $(this).addClass('in').removeClass('hidden');
                $(this).parents('li').addClass('target').removeClass('hidden');
                $(this).addClass('clicked');

            });
        }
//activity_var
       else if (activity != 0 && activity != null) {
            console.log('activity', activity)
            var value = sessionStorage.getItem('activity_' + project_id);
            global_selector = $('#activity-' + value);
//            $('article').removeClass('in').addClass('hidden');
            global_selector.parents().each(function () {
                $(this).addClass('in').removeClass('hidden')
            });
            global_selector.addClass('in target').removeClass('hidden');
            global_selector.parents('li').addClass('target').removeClass('hidden');
            $('ul.report_tree > li:not(.target)').addClass('hidden');
//            $('article').not('.target').parent('li').addClass('hidden');
//        $('ul.stdreport > li').not('.target').addClass('hidden');
        }

        else if (productivity != 0 && productivity != null) {
            console.log('productivity', productivity)
            var prod_value = sessionStorage.getItem('budget_productivity_' + project_id);
            console.log(prod_value)
            global_selector = $('#' + prod_value);
//            $('article').removeClass('in').addClass('hidden');
            global_selector.parents().each(function () {
                $(this).addClass('in').removeClass('hidden')
            });
            global_selector.addClass('in target').removeClass('hidden');
            global_selector.parents().each(function () {
                $(this).addClass('target').removeClass('hidden')
            });
            $('ul.report_tree > li:not(.target)').addClass('hidden');

        }

       else if (wbs != 0 && wbs != null) {
            console.log('wbs', wbs)
            var wbs_value = sessionStorage.getItem('wbs_' + project_id);
            console.log(wbs_value)
            global_selector = $('#col-' + wbs_value);
//            $('.level-container').removeClass('in').addClass('hidden');
            global_selector.parents().each(function () {
                $(this).addClass('in').removeClass('hidden')
            });
            global_selector.addClass('in').removeClass('hidden');
            global_selector.parents('li').addClass('target').removeClass('hidden');
            global_selector.children().children().children('article').addClass('in').removeClass('hidden');
            $('ul.report_tree > li:not(.target)').addClass('hidden');

        }

        else if (type != 0 && type != null) {
            var type_value = sessionStorage.getItem('dictionary_' + project_id);
            console.log(type_value)
            global_selector = $('#col-' + type_value);
//            $('.level-container').removeClass('in').addClass('hidden');
            global_selector.parents().each(function () {
                $(this).addClass('in').removeClass('hidden')
            });
            global_selector.addClass('in').removeClass('hidden');
            global_selector.parents('li').addClass('target').removeClass('hidden');
            global_selector.children().children().children('article').addClass('in').removeClass('hidden');
            $('ul.report_tree > li:not(.target)').addClass('hidden');

        }
        else{
            $('ul > li > article.collapse').addClass('in');
        }


        $('form').addClass('hidden');
        $('div.row').addClass('hidden')
        $('.back,.print').addClass('hidden')
        $('div.pull-right').children('a').addClass('hidden')
    })
    changeButtonBackroundColor();
    //        window.print();
</script>

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
