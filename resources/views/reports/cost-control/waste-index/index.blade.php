@extends('layouts.app')

@section('title', 'Waste Index')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Waste Index</h2>

        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back to Project
        </a>
    </div>
@endsection

@section('body')
    @include('reports.cost-control.waste-index._filters')

    <div class="horizontal-scroll">
        <section class="table-header">
            <table class="table">
                <thead>
                <tr>
                    <th class="col-sm-4">Resource</th>
                    <th class="col-sm-1">To Date Price/ Unit</th>
                    <th class="col-sm-1">To date Quantity</th>
                    <th class="col-sm-1">Allowable QTY</th>
                    <th class="col-sm-1">Quantity +/-</th>
                    <th class="col-sm-1">Material Allowable Cost</th>
                    <th class="col-sm-1">Material Actual Cost</th>
                    <th class="col-sm-1">Cost Variance - (waste)</th>
                    <th class="col-sm-1">Waste Percentage %</th>
                </tr>
                </thead>
            </table>
        </section>

        <section class="vertical-scroll">
            <table class="table">
                <tbody>
                @foreach($tree as $type)
                    @include('reports.cost-control.waste-index.type', ['depth' => 0])
                @endforeach
                </tbody>
            </table>
        </section>

    </div>
@endsection