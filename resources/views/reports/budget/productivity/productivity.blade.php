@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._productivity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Productivity Report</h2>
    <div class="pull-right">
        <a href="{{route('export.budget_productivity',
        ['project'=>$project])}}"
           class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i>
            Export</a>
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
    <style>
        .fixed {
            position: fixed;
            top: 0;
            height: 70px;
            z-index: 1;
        }
    </style>
@endsection
@section('body')
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$category)
            @include('reports.budget.productivity._recursive_productivity', ['category'=>$category,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
