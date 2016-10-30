@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2>Budget Summary</h2>
    <div class="pull-right">
        <a href="?print=1&paint=budget-summery" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/budgetsummery.jpg')}}">
@endsection
@section('body')

    <ul class="list-unstyled tree">
        @foreach($parents as $division)
            @include('std-activity._recursive_budget_summery',['division'=>$division ,'tree_level'=>0])
        @endforeach

    </ul>
@endsection