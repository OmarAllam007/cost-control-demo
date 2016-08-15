{{'@'}}extends('layouts.app')

{{'@'}}section('header')
    <h2>Edit {{$humanUp}}</h2>

    <form action="{{'{{'}} route('{{$viewPrefix}}.destroy', ${{$single}})}}" class="pull-right" method="post">
        @{{csrf_field()}} @{{method_field('delete')}}

        <a href="{{'{{'}} route('{{$viewPrefix}}.show', ${{$single}})}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{'{{'}} route('{{$viewPrefix}}.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
{{'@'}}stop

{{'@'}}section('body')
    {{'{{'}} Form::model(${{$single}}, ['route' => ['{{$viewPrefix}}.update', ${{$single}}]]) }}

        @{{ method_field('patch') }}

        {{'@'}}include('{{$viewPrefix}}._form')

    @{{ Form::close() }}
{{'@'}}stop
