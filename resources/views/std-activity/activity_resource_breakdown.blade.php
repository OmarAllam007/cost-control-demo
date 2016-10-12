@extends('layouts.app')
@section('header')
    <h2>Activity Resource BreakDown</h2>
@stop
@section('body')
    <ul class="list-unstyled tree">
        @foreach($data as $wbs_level=>$attributes)
            <li class="list-unstyled">
                <div class="tree--item">
                    <a href="#children" class="tree--item--label"><i
                                class="fa fa-chevron-circle-right"></i> {{$wbs_level}}
                    </a>
                </div>
                @if(isset($attributes['activities']))
                    <ul class="list-unstyled">
                        @foreach($attributes['activities'] as $item=>$value)
                            @foreach($value['cost_accounts'] as $account)

                                <li>
                                    <div class="tree--item collapse">
                                        <a href="#children-" class="tree--item--label"><i
                                                    class="fa fa-chevron-circle-right"></i> {{$item}}
                                        </a>
                                        <ul>
                                        <div class="tree--item collapse">
                                            <a href="#children-" class="tree--item--label"><i
                                                        class="fa fa-chevron-circle-right"></i> {{$account['cost_account']}}
                                            </a>

                                                <article id="children-">
                                                    <table class="table table-condensed table-striped "
                                                           style="margin: 3px; padding: 5px;">
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
                                        </div>
                                        </ul>
                                    </div>

                                </li>
                            @endforeach
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
@endsection
