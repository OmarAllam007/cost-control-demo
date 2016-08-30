@extends('layouts.app')

@section('header')
    <h2>WBS</h2>
    <a href="{{ route('wbs-level.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add Level</a>
@stop

@section('body')
    @if ($wbsLevels->total())
        <ul class="list-unstyled tree">
            @foreach($wbsLevels as $wbs_level)
                @include('wbs-level._recursive')
            @endforeach
        </ul>

        {{ $wbsLevels->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No WSB found</strong></div>
    @endif
@stop
