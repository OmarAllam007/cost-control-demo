@extends('layouts.app')

@section('header')
    <h2>Financial Period</h2>
    <a href="{{route('financial.create',$project)}}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add
        Financial Period</a>
@endsection
@section('body')

@endsection