@extends('layouts.app')
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr>
            <th class="col-xs-2">Code</th>
            <th class="col-xs-2">Discipline</th>
            <th class="col-xs-2">Dry Cost</th>
            <th class="col-xs-2">Budget Cost</th>
            <th class="col-xs-2">Difference</th>
            <th class="col-xs-2">Increase</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr>
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-2">{{$row['name']}}</td>
                <td class="col-xs-2">{{$row['dry_cost']}}</td>
                <td class="col-xs-2">{{$row['budget_cost']}}</td>
                <td class="col-xs-2">{{$row['difference']}}</td>
                <td class="col-xs-2">%{{number_format($row['increase'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-2" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-2">{{$total['dry_cost']}}</td>
            <td class="col-xs-2">{{$total['budget_cost']}}</td>
            <td class="col-xs-2">{{$total['difference']}}</td>
            <td class="col-xs-2">{{$total['increase']}}</td>
            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection