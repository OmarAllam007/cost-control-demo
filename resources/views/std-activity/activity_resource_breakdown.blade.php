@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2>Activity Resource BreakDown</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@stop

@section('body')
    <ul class="list-unstyled tree">
        @foreach($data as $wbs_level=>$attributes)
            @if(isset($attributes['activities']))
                <li>
                    <p><strong>{{$wbs_level}}</strong></p>
                    <ul class="list-unstyled">
                        @foreach($attributes['activities'] as $item=>$value)
                            @foreach($value['cost_accounts'] as $account)
                                <li class="tree--item">
                                    <p><strong>{{$item}}</strong></p>
                                    <ul>
                                        <li class="tree--item">
                                            <p><strong>{{$account['cost_account']}}</strong></p>
                                            <article id="children-">
                                                <table class="table table-condensed table-striped table-bordered">
                                                    <thead>
                                                    <tr>
                                                        {{--<th class="col-md-3 bg-success">Cost Account</th>--}}
                                                        <th class="col-md-3 bg-success">Resource Name</th>
                                                        <th class="col-md-2 bg-success">Price-Unit</th>
                                                        <th class="col-md-2 bg-success">Budget Unit</th>
                                                        <th class="col-md-2 bg-success">Budget Cost</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($account['resources'] as $resource)
                                                        <tr>
                                                            {{--<td class="col-md-3">{{$resource['type'] or ''}}</td>--}}
                                                            {{--<td class="col-md-3 ">{{$account['cost_account']}}</td>--}}
                                                            <td class="col-md-3">{{$resource['name']}}</td>
                                                            <td class="col-md-2">{{$resource['price_unit']}}</td>
                                                            <td class="col-md-2">{{$resource['budget_unit']}}</td>
                                                            <td class="col-md-2">{{$resource['budget_cost']}}</td>
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
