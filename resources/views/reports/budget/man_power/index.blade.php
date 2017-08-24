@extends('layouts.' . (request()->has('print') ? 'print' : 'app'))

@section('title', 'Man Power')

@section('header')
    <div class="display-flex">
        <h4 class="flex">Man Power &mdash; {{$project->name}}</h4>

        @if (!request()->has('print'))
            <div>
                <a href="?excel" class="btn btn-sm btn-info"><i class="fa fa-cloud-download"></i> Excel</a>
                <a href="?print=1&paint=productivity" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Print</a>
                <a href="{{route('project.show', $project)}}#Reports" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('body')
    <table class="table table-condensed table-striped table-bordered" id="report-table">
        <thead>
        <tr class="bg-primary">
            <th class="col-sm-4">Description</th>
            <th class="col-sm-2">Code</th>
            <th class="col-sm-2">Budget Cost</th>
            <th class="col-sm-2">Budget Unit</th>
            <th class="col-sm-2">Unit of Measure</th>
        </tr>
        </thead>
        <tbody>
        @foreach($resources as $resource)
            <tr>
                <td>{{$resource->resource_name}}</td>
                <td>{{$resource->resource_code}}</td>
                <td>{{number_format($resource->budget_cost, 2)}}</td>
                <td>{{number_format($resource->budget_unit, 2)}}</td>
                <td>{{$resource->measure_unit}}</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
        <tr class="info">
            <th colspan="2" class="text-right">Total</th>
            <th colspan="3">{{number_format($resources->sum('budget_cost'), 2)}}</th>
        </tr>
        </tfoot>
    </table>
@endsection

@section('javascript')
    <script>
        const rows = $('#report-table').find('tbody > tr');
        rows.click(function(e) {
            const isHighlighted = $(this).hasClass('highlighted');

            rows.removeClass('highlighted');
            if (!isHighlighted) {
                $(this).addClass('highlighted');
            }
        });
    </script>
@endsection

@section('css')
    <style>

        @media print {
            tr.hidden {
                display: table-row !important;
                visibility: visible;
            }


        }
        #report-table tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-table tbody tr.highlighted > td,
        #report-table thead tr.highlighted > th {
            background-color: #ffc;
        }
    </style>
@endsection