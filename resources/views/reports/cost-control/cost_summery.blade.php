@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_dry_building')
@endif
@section('header')
    <h2>Cost Summery report</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=cost-dry-building" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>--}}
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('body')

    <table class="table table-condensed">
        <thead class="output-cell">
        <tr>
            <th class="col-xs-2">Resource Type</th>
            <th class="col-xs-1">Base Line</th>
            <th class="col-xs-1">Previous Cost</th>
            <th class="col-xs-1">Previous (EV) Allowable</th>
            <th class="col-xs-1">to-date Cost</th>
            <th class="col-xs-1">to-date Variance</th>
            <th class="col-xs-1">Remaining Cost</th>
            <th class="col-xs-1">at Completion Cost +\-</th>
            <th class="col-xs-1">at Completion Variance +\-</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($data as $key=>$value)
                <td>{{$value['name']}}</td>
                <td>{{number_format($value['baseline'],2)}}</td>
                <td>{{number_format($value['previous_cost'],2)}}</td>
                <td>{{number_format($value['previous_allowable'],2)}}</td>
                <td>{{number_format($value['todate_cost'],2)}}</td>
                <td>{{number_format($value['todate_variance'],2)}}</td>
                <td>{{number_format($value['remaining_cost'],2)}}</td>
                <td>{{number_format($value['at_completion_cost'],2)}}</td>
                <td>{{number_format($value['cost_variance'],2)}}</td>

            @endforeach
        </tr>
        </tbody>
    </table>
@endsection