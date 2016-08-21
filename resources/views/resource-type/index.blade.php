@extends('layouts.app')

@section('header')
    <h2>Resource Type</h2>
    <a href="{{ route('resource-type.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add Resource Type</a>
@stop

@section('body')
    @if ($resource_levels->total())

        <ul class="list-unstyled tree">
            @foreach($resource_levels as $resource_level)
                @include('resource-type._recursive')
            @endforeach
        </ul>
        {{ $resource_levels->links() }}
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No resource type found</strong></div>
    @endif
@stop
