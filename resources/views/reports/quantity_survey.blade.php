@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 align="center">Quantity Survey</h2>
    <div class="pull-right">
        <a href="?print=1&paint=survey" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection
@section('image')
    <img src="{{asset('images/reports/qsSummery.jpg')}}">
@endsection
@section('body')
    <ul class="list-unstyled tree">
        @foreach($level_array as $level)
            <li class="list-unstyled">
                <div class="blue-second-level">
                    <strong>{{$level['name']}}</strong>
                </div>
                @if($level['activity_divisions'])
                    <ul class="list-unstyled">
                        <table class="table table-condensed">
                            <thead>
                            <tr class="blue-third-level">
                                <th class="col-xs-3">Activity Division</th>
                                <th class="col-xs-3">Boq Description</th>
                                <th class="col-xs-2">Cost Account</th>
                                <th class="col-xs-2">Engineering Quantity</th>
                                <th class="col-xs-2">Budget Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($level['activity_divisions']['division'] as $division)
                                <tr class="tbl-content">
                                    <td class="col-xs-3">

                                        {!!  nl2br($division) !!}<br>

                                    </td>

                                    <td class="col-xs-3">

                                        {{$level['activity_divisions']['boq_item_description']}}<br>

                                    </td>

                                    <td class="col-xs-2">
                                        {{$level['activity_divisions']['cost_account']}}
                                    </td>
                                    <td class="col-xs-2">
                                        {{number_format($level['activity_divisions']['budget_qty'],2)}}<br>
                                    </td>

                                    <td class="col-xs-2">
                                        {{number_format($level['activity_divisions']['eng_qty'],2)}}<br>
                                    </td>
                                    {{----}}
                                    {{--<td class="col-xs-2">--}}
                                    {{--{{$wbs[$level->id]['budget_qty']}}<br>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </ul>
                @endif
                @endforeach
            </li>
            @endsection
    </ul>