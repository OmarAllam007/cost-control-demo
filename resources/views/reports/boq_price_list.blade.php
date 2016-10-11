@extends('layouts.app')
@section('header')
    <h2>BOQ Price List</h2>
    <a href="{{ route('wbs-level.create') }} " class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Add Level</a>
@stop
@section('body')
    <ul class="list-unstyled tree">
        @foreach($data as $wbs_level=>$attributes)
            @include('reports._recursive_boq_price_list')
        @endforeach
    </ul>
@stop
