@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Standard Activity</h2>
    <div class="pull-right">
        <a href="{{route('export.budget_std_Activity',
        ['project'=>$project])}}"
            class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i>
            Export</a>

        <a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/standard-activity.jpg')}}" height="80%">
@endsection
@section('body')
    <ul class="list-unstyled tree">
        @foreach($parents as $division)
            @include('std-activity._recursive_report', ['division'=>$division,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>


    </script>
@endsection