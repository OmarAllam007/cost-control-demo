@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 align="center">Quantity Survey Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=survey" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <ul class="list-unstyled tree">
        @foreach($level_array as $level)
            <li>
                @if($level['activity_divisions'])
                    @foreach($level['activity_divisions'] as $division)
                        <ul class="list-unstyled">
                            <li class="list-unstyled">
                                @if(isset($division['name']))
                                    <div class="blue-second-level">
                                        <strong>{{$division['name']}}</strong>
                                    </div>
                                @endif
                                @foreach($division['activities'] as $activity)
                                    <ul class="list-unstyled">
                                        <li class="list-unstyled">
                                            @if(isset($activity['name']))
                                                <div class="blue-third-level">
                                                    <strong>{{$activity['name']}}</strong>
                                                </div>
                                        @endif
                                        <li>
                                            @if(isset($level['name']))
                                                <div class="blue-first-level">
                                                    <strong>{{$level['name']}}</strong>
                                                </div>
                                            @endif
                                        </li>
                                        <table class="table table-condensed">
                                            <thead class="blue-fourth-level">

                                            <th class="col-xs-3">Cost Account</th>
                                            <th class="col-xs-3">Boq Description</th>
                                            <th class="col-xs-2">Engineering Quantity</th>
                                            <th class="col-xs-2">Budget Quantity</th>
                                            <th class="col-xs-2">Unit of Measure</th>

                                            </thead>
                                            <tbody>
                                            @foreach($activity['cost_accounts'] as $cost_account)
                                                <tr style="font-size:10pt">
                                                    <td class="col-xs-3">
                                                        {{$cost_account['cost_account']}}
                                                    </td>
                                                    <td class="col-xs-3">
                                                        {{$cost_account['boq_name']}}
                                                    </td>
                                                    <td class="col-xs-2">
                                                        {{$cost_account['eng_qty']}}
                                                    </td>

                                                    <td class="col-xs-2">
                                                        {{$cost_account['budget_qty']}}
                                                    </td>
                                                    <td class="col-xs-2">
                                                        {{$cost_account['unit']}}

                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        </li>
                                    </ul>
                                @endforeach
                            </li>
                        </ul>
                    @endforeach
                @endif
            </li>
        @endforeach
    </ul>
@endsection
