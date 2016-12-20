@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._activity_resource_breakdown')
@endif
@section('header')
    <h2>Activity Resource BreakDown</h2>
    <div class="pull-right">
        <a href="?print=1&paint=activity-resource" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop
@section('image')
    <img src="{{asset('images/reports/activity-breakdown.jpg')}}">
@endsection
@section('body')
    <ul class="list-unstyled tree">
        @foreach($data as $wbs_level=>$attributes)

            @if(isset($attributes['activities']))
                <li>
                    <p class="blue-second-level"><strong>{{$wbs_level}}</strong><span class="pull-right">{{number_format($attributes['activities_total_cost'],2)}}</span></p>
                    <ul class="list-unstyled">
                        @foreach($attributes['activities'] as $item=>$value)
                            <li>
                            <p class="blue-third-level"><strong>{{$item}}</strong><span class="pull-right">{{number_format($value['activity_total_cost'],2)}}</span></p>
                            @foreach($value['cost_accounts'] as $account)
                                    <ul>
                                        <li class="tree--item">
                                            <p class="blue-fourth-level">
                                                {{$account['cost_account']}} - <abbr>({{$account['boq_description']}}
                                                    )</abbr><span class="pull-right">{{number_format($account['account_total_cost'],2)}}</span></p>
                                            <article id="children-">
                                                <table class="table table-condensed ">
                                                    <thead>
                                                    <tr class="tbl-children-division">
                                                        {{--<th class="col-md-3 bg-success">Cost Account</th>--}}
                                                        <th class="col-md-3">Resource Name</th>
                                                        <th class="col-md-2">Price-Unit</th>
                                                        <th class="col-md-3">Unit of Measure</th>
                                                        <th class="col-md-2">Budget Unit</th>
                                                        <th class="col-md-2">Budget Cost</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($account['resources'] as $resource)
                                                        <tr class="tbl-content">
                                                            <td class="col-md-3">{{$resource['name']}}</td>

                                                            <td class="col-md-2">{{number_format($resource['price_unit'],2)}}</td>
                                                            <td class="col-md-3">{{$resource['unit']}}</td>
                                                            <td class="col-md-2">{{number_format($resource['budget_unit'],2)}}</td>
                                                            <td class="col-md-2">{{number_format($resource['budget_cost'],2)}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </article>
                                        </li>
                                    </ul>

                                </li>
                            @endforeach
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    </ul>
@endsection
