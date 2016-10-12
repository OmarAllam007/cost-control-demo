@extends('layouts.app')
@section('header')
    <h2>BOQ Price List</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@stop
@section('body')
    <ul class="list-unstyled tree">
            @include('reports._recursive_boq_price_list')
    </ul>
@stop
