{{'@'}}extends('layouts.app')

{{'@'}}section('header')
    <h2>Add {{$humanUp}}</h2>

    <a href="{{'{{'}} route('{{$viewPrefix}}.index') }}" class="btn btn-sm btn-default pull-right"><i class="fa fa-chevron-left"></i> Back</a>
{{'@'}}stop

{{'@'}}section('body')
    {{'{{'}} Form::open(['route' => '{{$viewPrefix}}.store']) }}

        {{'@'}}include('{{$viewPrefix}}._form')

    @{{ Form::close() }}
{{'@'}}stop
