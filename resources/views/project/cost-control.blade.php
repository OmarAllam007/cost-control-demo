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
<style>
    .hvr-float-shadow {
        display: inline-block;
        vertical-align: middle;
        -webkit-transform: perspective(1px) translateZ(0);
        transform: perspective(1px) translateZ(0);
        box-shadow: 0 0 1px transparent;
        position: relative;
        -webkit-transition-duration: 0.3s;
        transition-duration: 0.3s;
        -webkit-transition-property: transform;
        transition-property: transform;

    }
    .hvr-float-shadow:before {
        pointer-events: none;
        position: absolute;
        z-index: -1;
        content: '';
        top: 100%;
        left: 5%;
        height: 10px;
        width: 90%;
        opacity: 0;
        background: -webkit-radial-gradient(center, ellipse, rgba(0, 0, 0, 0.35) 0%, transparent 80%);
        background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.35) 0%, transparent 80%);
        /* W3C */
        -webkit-transition-duration: 0.3s;
        transition-duration: 0.3s;
        -webkit-transition-property: transform, opacity;
        transition-property: transform, opacity;
    }
    .hvr-float-shadow:hover, .hvr-float-shadow:focus, .hvr-float-shadow:active {
        -webkit-transform: translateY(-5px);
        transform: translateY(-5px);
        /* move the element up by 5px */
    }
    .hvr-float-shadow:hover:before, .hvr-float-shadow:focus:before, .hvr-float-shadow:active:before {
        opacity: 1;
        -webkit-transform: translateY(5px);
        transform: translateY(5px);
        /* move the element down by 5px (it will stay in place because it's attached to the element that also moves up 5px) */
    }

</style>
@stop

@section('body')

    <nav id="project-nav" class="project-nav btn-toolbar pull-right">
        <a href="#datasheet" class="btn btn-primary btn-sm btn-outline"><i class="fa fa-table"></i> Data sheet</a>
        <a href="#resources" class="btn btn-info btn-sm btn-outline">Resources</a>
        <a href="#periods" class="btn btn-sm btn-violet btn-outline"><i class="fa fa-calendar"></i> Financial Periods</a>
        <a href="#CostControlReports" class="btn btn-success btn-sm btn-outline"><i class="fa fa-bar-chart"></i> Reports</a>
    </nav>
    <div class="clearfix"></div>


    @include('project.cost-control.datasheet')

    @include('project.cost-control.periods')
    @include('project.cost-control._report')


@stop

@section('javascript')
    <script>
        $(function () {
            $('.project-tab').hide();
//            $('#datasheet').show();

            var projectNav = $('#project-nav').on('click', 'a', function (e) {
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