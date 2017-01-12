@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._man-power')
@endif
@section('header')
    <h2>BUDGET BY NUMBERS</h2>
    <div class="pull-right">
        <a href="?print=1&paint=budget-number" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop

@section('body')
    @if(count($resources))
    <div class="tree--item blue-third-level" style="color: black">
        <a href="#1" data-toggle="collapse">{{$root}}</a>
    </div>

    <article id="1" class="tree--child collapse">
        <table class="table table-condensed  " style="margin: 3px; padding: 5px;">
            <thead>
            <tr class="tbl-children-division">
                <th class="col-md-3">Description</th>
                <th class="col-md-3">Budget Cost</th>
                <th class="col-md-2">Budget Unit</th>
                <th class="col-md-1">Unit of measure</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resources as $resource)

                @if($resource['id'])

                    <tr>
                        <td class="col-md-3 ">{{$resource['name'] or ''}}</td>
                        <td class="col-md-3">{{number_format($resource['budget_cost'], 2)}}</td>
                        <td class="col-md-2">{{number_format($resource['budget_unit'], 2)}}</td>
                        <td class="col-md-1">{{$resource['unit']  or ''}}</td>
                    </tr>
                @endif
            @endforeach
            <tr class="tbl-children-division"> <td>Total</td>
                <td>{{number_format($total_budget_cost,2)}}</td>
                <td>{{number_format($total_budget_unit,2)}}</td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </article>
    @endif
@stop