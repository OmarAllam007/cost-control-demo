@extends('layouts.app')
@section('header')
    <h2 align="center">Resource Dictionary</h2>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
@endsection
@section('body')
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th class="col-xs-1">Resource-Type</th>
            <th class="col-xs-2">RESOURCE DIVISION</th>
            <th class="col-xs-1">CODE</th>
            <th class="col-xs-1">RESOURCE NAME</th>
            <th class="col-xs-1">UNIT</th>
            <th class="col-xs-1">RATE</th>
            <th class="col-xs-1">SUPPLIER/SUBCON.</th>
            <th class="col-xs-1">REFERENCE</th>
            <th class="col-xs-1">Waste %</th>
            <th class="col-xs-1">BUDGET UNIT</th>
            <th class="col-xs-1">BUDGET COST</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $root=>$divisions)
            <?php $counter=0;?>
            @foreach($divisions['divisions'] as $divIndex => $resourceType)
                @foreach($divisions['divisions'][$divIndex]['resources'] as $index => $resource)
                    <tr>
                        <td class="col-xs-1">
                            @if ($counter == 0)
                                {{$root}}
                            @endif
                        </td>
                            <td class="col-xs-2">
                                @if ($counter == 0)
                                    {{$resourceType['name']}}
                                @endif
                            </td>
                        <td class="col-xs-1">
                            {{$resource['code']}}
                        </td>
                        <td class="col-xs-1">
                            {{$resource['name']}}
                        </td>
                        <td class="col-xs-1">
                            {{$resource['unit']}}
                        </td>
                        <td class="col-xs-1">
                            {{$resource['rate']}}
                        </td>
                        <td class="col-xs-1">
                            {{$resource['partner']}}
                        </td>

                        <td class="col-xs-1">
                            {{$resource['reference']}}
                        </td>
                        <td class="col-xs-1">{{$resource['waste']}}%</td>
                        <td class="col-xs-1">{{$resource['budget_unit']}}</td>
                        <td class="col-xs-1">{{$resource['budget_cost']}}</td>
                    <?php $counter++;?>
                @endforeach
            @endforeach
        @endforeach
        </tbody>
    </table>

@endsection