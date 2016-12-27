@extends('layouts.app')

@section('header')
    <h2>Edit Resources</h2>

    <form action="{{ route('resources.destroy', $resources)}}" class="pull-right" method="post">

        @can('delete', 'resources')
        {{csrf_field()}} {{method_field('delete')}}
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @endcan

        @if ($resources->project_id)
            <a href="{{ route('project.show', $resources->project_id)}}#resources" class="btn btn-sm btn-default">
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
    {{ Form::model($resources, ['route' => ['resources.update', 'resources'=>$resources,'project_id'=>request('project_id')], 'method' => 'PATCH']) }}

    @include('resources._form', ['override' => $resources->project_id])

    {{ Form::close() }}
@stop
