@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    {{--@include('reports.all._budget_dry_building')--}}
@endif
@section('header')
    <h2>Cost Performance By Significant Material</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=cost-dry-building" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>--}}
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('body')
    <ul class="list-unstyled tree">

    </ul>

@endsection