@extends('layouts.app')
@section('body')

        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-1">Type</th>
                <th class="col-xs-1">Code</th>
                <th class="col-xs-1">Resource Name</th>
                <th class="col-xs-1">Unit</th>
                <th class="col-xs-1">Rate</th>
                <th class="col-xs-1">SUPPLIER/SUBCON.</th>
                <th class="col-xs-1">Reference</th>
                <th class="col-xs-1">Waste</th>
                <th class="col-xs-1">Budget Qty</th>
                <th class="col-xs-1">Budget Cost</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($resources as $resource)

{{--{{dump($resource->toArray())}}--}}
                <tr>
                    <td class="col-xs-1">{{$resource->types->path or ''}}</td>
                    <td class="col-xs-1">{{$resource->resource_code}}</td>
                    <td class="col-xs-1">{{$resource->name}}</td>
                    <td class="col-xs-1">{{$resource->versionFor($project->id)->units->type or ''}}</td>
                    <td class="col-xs-1">{{number_format($resource->versionFor($project->id)->rate, 2)}}</td>
                    <td class="col-xs-1">{{$resource->parteners->name or ''}}</td>
                    <td class="col-xs-1">{{$resource->reference}}</td>
                    <td class="col-xs-1">{{number_format($resource->versionFor($project->id)->waste, 2)}} %</td>
                    <td class="col-xs-1">{{(\App\StdActivityResource::where('resource_id',$resource->id)->pluck('budget_qty')->first())*(1 + ($resource->waste /100))}}</td>

                    <td class="col-xs-1">{{(\App\StdActivityResource::where('resource_id',$resource->id)->pluck('budget_qty')->first())* $resource->rate}}</td>

                </tr>
            @endforeach
            </tbody>
        </table>

@endsection