@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2 align="center">Quantity Survey</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
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
            <div class="tree--item" style="background-color: #154360;
  color:white;
  padding: 3px;
  font-weight: bold;">
                <strong>{{$level['name']}}</strong>
            </div>
            @if($level['activity_divisions'])
                <ul class="list-unstyled">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr class="row-shadow items-style">
                            <th class="col-xs-3">Activity Division</th>
                            <th class="col-xs-3">Boq Description</th>
                            <th class="col-xs-2">Cost Account</th>
                            <th class="col-xs-2">Engineering Quantity</th>
                            <th class="col-xs-2">Budget Quantity</th>
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