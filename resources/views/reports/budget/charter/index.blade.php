@extends('layouts.' . (request('print')? 'print' : 'app'))

@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif

@section('title') Project Charter &mdash; {{$project->name}} @endsection

@section('header')
    <div class="display-flex">
        <h2 class="flex">Project Charter &mdash; {{$project->name}}</h2>

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

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            @include('reports.budget.charter._basic_info')

            @include('reports.budget.charter._brief')

            <h4 class="page-header">Project Budget Summary</h4>
            @include('reports.budget.charter._budget_by_discipline')
            @include('reports.budget.charter._budget_by_resource_type')
            @include('reports.budget.charter._discipline_brief')
            @include('reports.budget.charter._assumptions')
        </div>
    </div>


@endsection

@section('javascript')
    @php
        $disciplineColumns = $disciplines->map(function($discipline) {
            return [$discipline->discipline, $discipline->weight / 100];
        });

        $typeColumns = $resource_types->map(function($type) {
            return [$type->type, $type->weight / 100];
        });
    @endphp

    <script src="/js/d3.min.js"></script>
    <script src="/js/c3.min.js"></script>

    <script>
        // Disciplines report
        c3.generate({
            bindto: '#disciplines-chart',
            data: {
                type: 'pie', columns: {!! $disciplineColumns !!}
            }
        });

        // Resource Types report
        c3.generate({
            bindto: '#types-chart',
            data: {
                type: 'pie', columns: {!! $typeColumns !!}
            }
        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/c3.min.css">
    <style>
        .table > tbody > tr > td, .table > tbody > tr > th {
            font-size: 92%;
            line-height: 1.4;
        }

        h4.page-header {
            margin-top: 20px; margin-bottom: 10px;
        }


    </style>
@endsection