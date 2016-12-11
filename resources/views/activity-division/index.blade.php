@extends('layouts.app')

@section('header')
    <h2>Activity Divisions</h2>
    @can('write', 'std-activity')
    <a href="{{ route('activity-division.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add Division</a>
    @endcan
    {{--<a href="{{ route('division.import') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Upload project</a>--}}
@stop

@section('body')
    @if ($activityDivisions->total())
        <ul class="list-unstyled tree">
            @foreach($activityDivisions->sort() as $division)
                @include('activity-division._recursive', compact('division'))
            @endforeach
        </ul>

        <div class="text-center">
            {{ $activityDivisions->links() }}
        </div>
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No activity divisions found</strong></div>
    @endif
@stop
