@extends('layouts.app')

@section('header')
    <h2>{{ $project->name }}</h2>

    <nav class="btn-toolbar pull-right">



        <div class="btn-group">
            <a href="#import-links" class="btn btn-outline btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i
                        class="fa fa-cloud-upload"></i> Import <span class="caret"></span></a>
            <ul id="import-link" class="dropdown-menu">
                <li><a href="{{route('actual-material.import', $project)}}">Material</a></li>
                <li><a href="#labour">Labour</a></li>
                <li><a href="#invoice">Invoices</a></li>
                <li><a href="{{route('activity-map.import', $project)}}">Activity Mapping</a></li>
            </ul>
        </div>


        <a href="{{ route('project.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </nav>
@stop

@section('body')

    <nav id="project-nav" class="project-nav btn-toolbar pull-right">
        <a href="#datasheet" class="btn btn-primary btn-sm btn-outline"><i class="fa fa-table"></i> Data sheet</a>
        <a href="#resources" class="btn btn-info btn-sm btn-outline">Resources</a>
        <a href="#periods" class="btn btn-sm btn-violet btn-outline"><i class="fa fa-calendar"></i> Financial Periods</a>
        <a href="#reports" class="btn btn-success btn-sm btn-outline"><i class="fa fa-bar-chart"></i> Reports</a>
    </nav>
    <div class="clearfix"></div>


    @include('project.cost-control.datasheet')

    @include('project.cost-control.periods')

@stop

@section('javascript')
    <script>
        $(function(){
            $('.project-tab').hide();
//            $('#datasheet').show();

            var projectNav = $('#project-nav').on('click', 'a', function(e) {
                e.preventDefault();
                $('.project-tab').hide();
                $($(this).attr('href')).show();
                projectNav.find('a').removeClass('active');
                $(this).addClass('active');
            });

            projectNav.find('a:first').click();
        })
    </script>
    <script src="{{asset('/js/cost-control.js')}}"></script>
@endsection