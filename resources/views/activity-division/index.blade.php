@extends('layouts.app')

@section('header')
    <h2>Activity Divisions</h2>
    <a href="{{ route('activity-division.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add Division</a>
    {{--<a href="{{ route('division.import') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Upload project</a>--}}
@stop

@section('body')
    @if ($activityDivisions->total())
        <ul class="list-unstyled tree">
            @foreach($activityDivisions as $division)

                @include('activity-division._recursive', compact('division'))
            @endforeach
        </ul>
        {{ $activityDivisions->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No activity divisions found</strong></div>
    @endif
@stop
