@extends('layouts.' . (request('print')? 'print' : 'app'))

@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif

@section('header')
    <div class="display-flex">
        <h2 class="flex">High Priority Materials Report &mdash; {{$project->name}}</h2>

        @if (!request('print'))
        <div class="btn-toolbar">
            <a href="?excel" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> Export</a>
            <a href="?print=1" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Print</a>
            <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
        </div>
        @endif
    </div>
@endsection

@section('body')

    <table class="table table-bordered table-hover">
        <thead>
        <tr class="bg-primary">
            <th>Resource Name</th>
            <th>Resource Code</th>
            <th>Budget Unit</th>
            <th>Budget Cost</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($tree as $group)
            <tr class="bg-info">
                <th colspan="3">{{$group['name']}}</th>
                <th class="text-right">{{number_format($group['budget_cost'], 2)}}</th>
            </tr>

            @foreach($group['resources'] as $resource)
                <tr>
                    <td>{{$resource->name}}</td>
                    <td>{{$resource->resource_code}}</td>
                    <td class="text-right">{{number_format($resource->budget_unit, 2)}}</td>
                    <td class="text-right">{{number_format($resource->budget_cost, 2)}}</td>
                </tr>
            @endforeach

        @endforeach
        </tbody>
    </table>

@endsection

@section('javascript')
@endsection
