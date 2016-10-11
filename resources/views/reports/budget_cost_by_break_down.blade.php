@extends('layouts.app')
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-3">Resource Type</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-3">% Weight</th>

        </tr>
        </thead>
        <tbody>
        @foreach($bd_resource as $row)

            <tr>
                <td class="col-xs-2">{{$row['resource_code']?:''}}</td>
                <td class="col-xs-3">{{$row['resource_type']}}</td>
                <td class="col-xs-2">{{$row['budget_cost']}}</td>
                <td class="col-xs-3">{{$row['weight']}} % </td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-3">Grand Total</td>
            <td class="col-xs-2">{{$total['total']}}</td>
            <td class="col-xs-3">{{$total['weight_total']}} %</td>
            </td>
        </tr>
        </tbody>
    </table>

@endsection