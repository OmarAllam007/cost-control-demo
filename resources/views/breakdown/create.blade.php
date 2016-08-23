@extends('layouts.app')

@section('header')

@endsection

@section()

    {{Form::open(['route' => 'breakdown.create'])}}

        @include('breakdown._form')

    {{Form::close()}}
@stop
