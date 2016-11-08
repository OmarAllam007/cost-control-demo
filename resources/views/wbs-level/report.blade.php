@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._wbs')
@endif
@section('header')
    <h1>WBS-LEVELS</h1>
    <div class="pull-right">
        <a href="?print=1&paint=wbs" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
        <link rel="stylesheet" href="{{asset('/css/print.css')}}">

    </div>

@endsection
@section('image')
    <img src="{{asset('images/reports/wbs-level.jpg')}}">
@endsection
@section('body')
    @if ($project->wbs_tree->count())
        <table class="table table-condensed table-bordered">
            <thead>
            <tr>
                <th width="25%" class="blue-first-level">WBS Level 1</th>
                <th width="25%" class="blue-first-level">WBS Level 2</th>
                <th width="25%" class="blue-first-level">WBS Level 3</th>
                <th width="25%" class="blue-first-level">WBS Level 4</th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->wbs_tree as $wbs_level)
                @include('wbs-level._recursive_report', ['wbs_level' => $wbs_level, 'tree_level' => 0,'child'=>false])
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No WBS found</div>
    @endif
@endsection