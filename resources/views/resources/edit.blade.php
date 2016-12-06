@extends('layouts.app')

@section('header')
    <h2>Edit Resources</h2>

    <form action="{{ route('resources.destroy', $resources)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        {{--<a href="{{ route('resources.show', $resources)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i>
            Show</a>--}}
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @if ($resource->project_id)
            <a href="{{ route('project.show', $resource->project)}}#resources" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        @else
            <a href="{{ route('resources.index')}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        @endif
    </form>
@stop

@section('body')
    {{ Form::model($resources, ['route' => ['resources.update', $resources], 'method' => 'PATCH']) }}

    @include('resources._form', ['override' => $resources->project_id])

    {{ Form::close() }}
@stop
