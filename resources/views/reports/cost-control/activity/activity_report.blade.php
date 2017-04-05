@extends('layouts.app')

@if(request('all'))
    @include('reports.all._standard-activity')
@endif

@section('header')
    <h2 class="">{{$project->name}} - Activity report</h2>
    <div class="pull-right">
        {{--<a href="?print=1" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i> Print</a>--}}
        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')

    <table class="table table-bordered" id="activity-table">
        <thead>
        <tr>
            <th>Activity</th>

            <th>Base Line</th>

            <th>Previous Cost</th>
            <th>Previous Allowable</th>
            <th>Previous Var</th>

            <th>To Date Cost</th>
            <th>Allowable (EV) Cost</th>
            <th>To Date Cost Var</th>

            <th>Remaining Cost</th>
            <th>At Completion Cost</th>
            <th>Cost Variance</th>
        </tr>
        </thead>

        <tbody>

            @foreach($tree->where('parent', '') as $key => $level)
                @include('reports.cost-control.activity._wbs')
            @endforeach

        </tbody>
    </table>
@endsection

@section('javascript')

@endsection