@extends('home.master-data')

@section('title', 'Resource Types')

@section('header')
    <h2>Resource Type</h2>
    <div class="btn-toolbar pull-right">
        <a href="{{ route('resource-type.create') }} " class="btn btn-sm btn-primary pull-right">
            <i class="fa fa-plus"></i> Add Resource Type
        </a>
    </div>
@stop

@section('content')
    @if ($resource_types->count())

        <ul class="list-unstyled tree">
            @foreach($resource_types as $resource_type)
                @include('resource-type._recursive')
            @endforeach
        </ul>
    @else
        <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <strong>No resource type found</strong>
        </div>
    @endif
@stop
