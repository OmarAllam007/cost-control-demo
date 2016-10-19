@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2 class="">Standard Activity</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/standard-activity.jpg')}}">
@endsection
@section('body')
    <ul class="list-unstyled tree">
        @foreach($parents as $division)
            @include('std-activity._recursive_report', compact('division'))
        @endforeach
    </ul>
@endsection