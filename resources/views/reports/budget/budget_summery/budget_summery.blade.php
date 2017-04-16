@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Budget Summery Report</h2>
    <div class="pull-right">
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
    <li class="list-unstyled" style="text-align:center;box-shadow: 5px 5px 5px #888888;
">
        <div class="tree--item">
            <div class="tree--item--label blue-first-level">
                <h5 style="font-size:20pt;font-family: 'Lucida Grande'"><strong>Total Project Budget Cost: {{number_format($total_budget,2)}} </strong></h5>
            </div>
        </div>

    </li>
    <br>
    <ul class="list-unstyled tree report_tree">
        @foreach($tree as $parentKey=>$division)
            @include('reports.budget.budget_summery._recursive_report', ['division'=>$division,'tree_level'=>0])
        @endforeach
    </ul>
@endsection

@section('javascript')

@endsection
