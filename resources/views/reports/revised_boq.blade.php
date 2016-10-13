@extends('layouts.app')
@section('header')
    <h2 align="center">Revised Boq</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')

    <table class="table table-condensed table-striped table-fixed">
        <thead>
        <tr style="background-color:yellow">
            <th class="col-xs-2">Code</th>
            <th class="col-xs-3">BUILDING NAME</th>
            <th class="col-xs-3">REVISED BOQ</th>
            <th class="col-xs-2">ORIGINAL BOQ</th>
            <th class="col-xs-2">% Weight</th>

        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)

            <tr>
                <td class="col-xs-2">{{$row['code']}}</td>
                <td class="col-xs-3">{{$row['name']}}</td>
                <td class="col-xs-3">{{number_format($row['revised_boq'],2)}}</td>
                <td class="col-xs-2">{{number_format($row['original_boq'],2)}}</td>
                <td class="col-xs-2">% {{number_format($row['weight'],2)}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-3" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-3">{{number_format($total['revised_boq'],2)}}</td>
            <td class="col-xs-2">{{number_format($total['original_boq'],2)}}</td>
            <td class="col-xs-2">% {{number_format($total['weight'],2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection