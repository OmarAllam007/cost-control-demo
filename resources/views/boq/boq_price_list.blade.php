@extends('layouts.app')
@section('header')
    <h1>BOQ PRICE LIST</h1>
    <a href="{{URL::previous()}}" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')
    @foreach($wbs_levels as $level)
        <li class="list-unstyled">
            <div class="tree--item">
                <a href="#children-{{$level->id}}" class="tree--item--label" data-toggle="collapse"><i
                            class="fa fa-chevron-circle-right"></i> {{$level->name}}
                    <small class="text-muted">({{$level->code}})</small>
                </a>
            </div>
            @if($wbs[$level->id]['divisions'] && $boq_items[$level->id]['divisions']->count())
                <ul class="list-unstyled collapse" id="children-{{$level->id}}">
                    <table class="table table-condensed table-striped">
                        <thead>
                        <tr>
                            <th class="col-xs-3">Activity Division</th>
                            <th class="col-xs-3">Boq Description</th>
                            <th class="col-xs-2">Cost Account</th>
                            <th class="col-xs-2">Engineering Quantity</th>
                            <th class="col-xs-2">Budget Quantity</th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-xs-3">
                                @foreach($boq_items[$level->id]['divisions'] as $division)
                                    {!!  nl2br($division->name) !!}<br>
                                @endforeach
                            </td>
                            {{--                {{dd($boq_items[$level->id]['boq_description'])}}--}}
                            <td class="col-xs-3">
                                @foreach($boq_items[$level->id]['boq_description'] as $boq)
                                    {!!  nl2br($boq->description) !!}<br>
                                @endforeach
                            </td>

                            <td class="col-xs-2">
                                @foreach($boq_items[$level->id]['boq_description'] as $boq)
                                    {!!  nl2br($boq->cost_account) !!}<br>
                                @endforeach
                            </td>
                            <td class="col-xs-2">
                                {{$boq_items[$level->id]['eng_qty']}}<br>
                            </td>

                            <td class="col-xs-2">
                                {{$boq_items[$level->id]['budget_qty']}}<br>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </ul>
            @endif
            @endforeach
        </li>
@endsection