@extends('layouts.app')

@section('header')
<h2 class="panel-title">Add Breakdown</h2>
@endsection

@section('body')

    {{Form::open(['route' => 'breakdown.create'])}}
        
            @include('breakdown._form')
        
    {{Form::close()}}
@stop