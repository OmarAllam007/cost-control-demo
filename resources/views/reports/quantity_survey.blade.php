@extends('layouts.app')
@section('header')
    <h2 align="center">Quantity Survey</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')
    @foreach($level_array as $level)
        <li class="list-unstyled">
            <div class="tree--item">
                <a href="#children-{{$level['id']}}" class="tree--item--label" data-toggle="collapse"><i
                            class="fa fa-chevron-circle-right"></i> {{$level['name']}}
                    <small class="text-muted"></small>
                </a>
            </div>
            @if($level['activity_divisions'])
                <ul class="list-unstyled collapse" id="children-{{$level['id']}}">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-3">Activity Division</th>
                            <th class="col-xs-3">Boq Description</th>
                            <th class="col-xs-2">Cost Account</th>
                            <th class="col-xs-2">Engineering Quantity</th>
                            <th class="col-xs-2">Budget Quantity</th>
{{----}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($level['activity_divisions']['division'] as $division)
                        <tr>
                            <td class="col-xs-3">

                                    {!!  nl2br($division) !!}<br>

                            </td>
                                <td class="col-xs-3">
                                    @foreach($level['activity_divisions']['boq_item_description'] as $boq)
                                    {!!  nl2br($boq) !!}<br>
                                    @endforeach
                                </td>

                            <td class="col-xs-2">
                                @foreach($level['activity_divisions']['cost_account'] as $account)
                                {!!  nl2br($account) !!}<br>
                                @endforeach
                            </td>
                            <td class="col-xs-2">
                                    {{$level['activity_divisions']['budget_qty']}}<br>
                            </td>

                            <td class="col-xs-2">
                                    {{$level['activity_divisions']['eng_qty']}}<br>
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