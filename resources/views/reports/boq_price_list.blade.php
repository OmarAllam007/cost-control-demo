@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2>BOQ Price List</h2>
    <div class="pull-right">
    <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
    <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/boq-price.jpg')}}">
@endsection
@section('body')
    <ul class="list-unstyled tree">
            @include('reports._recursive_boq_price_list')
    </ul>
@stop
