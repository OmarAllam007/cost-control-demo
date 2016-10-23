@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2 align="center">Revised Boq</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/revised-boq.jpg')}}">
@endsection
@section('body')

    <table class="table table-condensed">
        <thead>
        <tr class="row-shadow">
            <th class="col-xs-2" style="background-color:#446CB3; color: white">Code</th>
            <th class="col-xs-3" style="background-color:#446CB3; color: white">BUILDING NAME</th>
            <th class="col-xs-3" style="background-color:#446CB3; color: white">REVISED BOQ</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">ORIGINAL BOQ</th>
            <th class="col-xs-2" style="background-color:#446CB3; color: white">% Weight</th>

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