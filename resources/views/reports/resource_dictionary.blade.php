@extends('layouts.app')
@section('body')
    <table class="table table-condensed table-striped table-fixed">
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
            @foreach($divisions['divisions'] as $divIndex => $resourceType)
                @foreach($divisions['resources'][$resourceType->id] as $index => $resource)
                    <tr>
                        <td class="col-xs-1">
                            @if ($divIndex == 0 && $index == 0)
                                {{$root}}
                            @endif
                        </td>
                            <td class="col-xs-2">
                                @if ($index == 0)
                                {{$resourceType->name}}
                                @endif
                            </td>
                        <td class="col-xs-1">
                            {{$resource->resource_code}}
                        </td>
                        <td class="col-xs-1">
                            {{$resource->name}}
                        </td>
                        <td class="col-xs-1">
                            {{ isset(\App\Unit::where('id',$resource->unit)->first()->type)?\App\Unit::where('id',$resource->unit)->first()->type:''}}
                        </td>
                        <td class="col-xs-1">{{$resource->rate}}</td>
                        <td class="col-xs-1">{{isset(\App\BusinessPartner::where('id',$resource->business_partner_id)->first()->name)?\App\BusinessPartner::where('id',$resource->business_partner_id)->first()->name:''}}</td>

                        <td class="col-xs-1">{{$resource->reference}}</td>
                        <td class="col-xs-1">{{$resource->waste}} %</td>
                        <td class="col-xs-1"></td>

                @endforeach
            @endforeach
        @endforeach
        </tbody>
    </table>

@endsection