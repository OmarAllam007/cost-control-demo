@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
<h2 class="panel-title">Add Breakdown</h2>
<a href="{{route('project.show', request('project_id'))}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection

@section('body')
    {{Form::open(['route' => 'breakdown.store'])}}
        @include('breakdown._form')
    {{Form::close()}}
@stop
