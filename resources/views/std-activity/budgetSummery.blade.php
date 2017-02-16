@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget-summery')
@endif
@section('header')
    <h2>{{$project->name}} - Budget Summary</h2>
    <div class="pull-right">
        <a href="?print=1&paint=budget-summery" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/budgetsummery.jpg')}}">
@endsection
@section('body')
    <li class="list-unstyled" style="text-align:center;box-shadow: 5px 5px 5px #888888;
">
        <div class="tree--item">
            <div class="tree--item--label blue-first-level">
        <h5 style="font-size:20pt;font-family: 'Lucida Grande'"><strong>Total Project Cost : {{number_format($total_project,2)}} SR </strong></h5>
            </div>
        </div>

    </li>
    <br>
    <ul class="list-unstyled tree">
        @foreach($data as $key=>$division)
            @include('std-activity._recursive_budget_summery',['division'=>$division ,'tree_level'=>0])
        @endforeach

    </ul>
@endsection