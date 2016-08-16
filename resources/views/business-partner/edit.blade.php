@extends('layouts.app')

@section('header')
    <h2>Edit Business partner</h2>

    <form action="{{ route('business-partner.destroy', $business_partner)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('business-partner.show', $business_partner)}}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Show</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('business-partner.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    {{ Form::model($business_partner, ['route' => ['business-partner.update', $business_partner]]) }}

        {{ method_field('patch') }}

        @include('business-partner._form')

    {{ Form::close() }}
@stop
