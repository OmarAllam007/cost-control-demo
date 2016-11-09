@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Edit Boq</h2>

    <form action="{{ route('boq.destroy', $boq)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('project.show', $boq->project_id)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($boq, ['route' => ['boq.update', $boq]]) }}

        {{ method_field('patch') }}

        @include('boq._form')

    {{ Form::close() }}
@stop
