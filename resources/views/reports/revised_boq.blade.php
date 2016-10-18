@extends('layouts.' . (request('print')? 'print' : 'app'))
@section('header')
    <h2 align="center">Revised Boq</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
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
                <td class="col-xs-3">{{$row['revised_boq']}}</td>
                <td class="col-xs-2">{{$row['original_boq']}}</td>
                <td class="col-xs-2">% {{$row['weight']}}</td>

            </tr>
        @endforeach
        <tr style="border-top: solid #000000">
            <td class="col-xs-2"></td>
            <td class="col-xs-3" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-3">{{$total['revised_boq']}}</td>
            <td class="col-xs-2">{{$total['original_boq']}}</td>
            <td class="col-xs-2">% {{$total['weight']}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>

@endsection