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
</table>
@yield('image')
<p style="page-break-before: always"></p>
