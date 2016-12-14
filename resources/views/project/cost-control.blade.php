@extends('layouts.app')

@section('header')
    <h2>{{ $project->name }}</h2>

    <nav class="btn-toolbar pull-right">

        <a class="btn btn-outline btn-sm btn-info" href="{{route('activity-map.import', $project)}}">Activity
            Mapping</a>

        <div class="btn-group">
            <a href="#import-links" class="btn btn-outline btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><i
                        class="fa fa-cloud-upload"></i> Import <span class="caret"></span></a>
            <ul id="import-link" class="dropdown-menu">
                <li><a href="{{route('actual-material.import', $project)}}">Material</a></li>
                <li><a href="#labour">Labour</a></li>
                <li><a href="#invoice">Invoices</a></li>
            </ul>
        </div>


        <a href="{{ route('project.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </nav>
@stop

@section('body')

    <nav id="data-sheet-nav" class="btn-toolbar pull-right">
        <a href="#wbs" class="btn btn-primary btn-sm btn-outline"><i class="fa fa-table"></i> Data sheet</a>
        <a href="#resources" class="btn btn-info btn-sm btn-outline">Resources</a>
        <a href="#periods" class="btn btn-sm btn-violet btn-outline"><i class="fa fa-calendar"></i> Financial Periods</a>
        <a href="#reports" class="btn btn-success btn-sm btn-outline"><i class="fa fa-bar-chart"></i> Reports</a>
    </nav>


    @include('project.cost-control.datasheet')
    @include('project.cost-control.periods')

@stop

@section('javascript')
    <script src="{{asset('/js/cost-control.js')}}"></script>
@endsection