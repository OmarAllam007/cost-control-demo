@extends('layouts.app')

@section('header')
    <h2>Edit Resources</h2>

    <form action="{{ route('resources.destroy', $resources)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        {{--<a href="{{ route('resources.show', $resources)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i>
            Show</a>--}}
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('resources.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($resources, ['route' => ['resources.update', $resources]]) }}

        {{ method_field('PATCH') }}

        @include('resources._form')

    {{ Form::close() }}
@stop
