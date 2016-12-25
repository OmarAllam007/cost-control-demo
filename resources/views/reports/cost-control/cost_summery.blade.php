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
        <tr style="border: 2px solid black">
            <td></td>
            <td style="border: 2px solid black;text-align: center">BaseLine</td>
            <td colspan="3" style="border: 2px solid black;text-align: center">Previous</td>
            <td colspan="3" style="border: 2px solid black;text-align: center">To-Date</td>
            <td colspan="2" style="border: 2px solid black;text-align: center">Remaining</td>
        </tr>
        <tr>
            <th class="col-xs-2" style="border: 2px solid black;text-align: center">Resource Type</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Base Line</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous (EV) Allowable</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Variance</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Allowable (EV) Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">to-date Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Cost Variance +\-</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Remaining Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">at Completion Cost +\-</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $key=>$value)
            <tr>
                <td style="border: 2px solid black;text-align: center">{{$key}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['budget_cost']??0,2) }}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['previous_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['previous_allowable']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['previous_variance']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['allowable_ev_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['To_date_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['cost_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['remaining_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['completion_cost']??0,2)}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection