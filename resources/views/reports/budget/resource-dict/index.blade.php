@extends('layouts.' . (request()->has('print') ? 'print' : 'app'))

@section('title', 'Resource Dictionary')

@section('header')
    <div class="display-flex">
        <h4 class="flex">Resource Dictionary &mdash; {{$project->name}}</h4>

        @if (!request()->has('print'))
            <div>
                <a href="?excel" class="btn btn-sm btn-info"><i class="fa fa-cloud-download"></i> Excel</a>
                <a href="?print=1&paint=resource-dictionary" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print</a>
                <a href="{{route('project.show', $project)}}#Reports" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('body')
    <section class="header-table">
    <table class="table table-condensed table-bordered" id="report-head">
        <thead>
        <tr class="bg-primary">
            <th class="col-sm-2">Resource</th>
            <th class="col-sm-1">Code</th>
            <th class="col-sm-1">Rate</th>
            <th class="col-sm-1">Unit of measure</th>
            <th class="col-sm-2">Supplier/Subcontractor</th>
            <th class="col-sm-1">Reference</th>
            <th class="col-sm-1">Waste (%)</th>
            <th class="col-sm-1">Budget Unit</th>
            <th class="col-sm-1">Budget Cost</th>
            <th class="col-sm-1">Weight (%)</th>
        </tr>
        <tr class="info">
            <th colspan="8">Total</th>
            <th>{{number_format($tree->sum('budget_cost'), 2)}}</th>
            <th>{{number_format($tree->sum('weight'), 2)}}%</th>
        </tr>
        </thead>
    </table>
    </section>

    <section class="vertical-scroll">
        <table class="table table-condensed table-bordered" id="report-body">
            <tbody>
            @foreach($tree as $division)
                @include('reports.budget.resource-dict._recursive', ['division' => $division, 'depth' => 0])
            @endforeach
            </tbody>
        </table>
    </section>

@endsection

@section('javascript')
    <script>
        $('.open-level').click(function (e) {
            e.preventDefault();
            const target = $('.' + $(this).data('target'));

            if (target.hasClass('hidden')) {
                target.removeClass('hidden');
                $(this).find('i.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
            } else {
                closeRecursive(this);
            }
        });

        const rows = $('#report-table').find('tbody > tr');
        rows.click(function (e) {
            const isHighlighted = $(this).hasClass('highlighted');

            rows.removeClass('highlighted');
            if (!isHighlighted) {
                $(this).addClass('highlighted');
            }
        });

        function closeRecursive(elem) {
            const target = $('.' + $(elem).data('target'));
            target.addClass('hidden').each(function () {
                closeRecursive($(this).find('a'));
            });

            $(elem).find('i.fa').removeClass('fa-minus-square').addClass('fa-plus-square');
        }
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

        .vertical-scroll {
            max-height: 500px;
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .header-table {
            margin-right: 17px;
        }

        #report-body tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-body tbody tr.highlighted > td {
            background-color: #ffc;
        }

        #report-body > tbody > tr > td {
            border-color: #f7f7f7;
        }

        .level-0 td {
            background: #f7f7f7;
        }

        .level-1 td {
            background: #ededed;
        }

        .level-2 td {
            background: #e6e6e6;
        }

        .level-3 td {
            background: #dedede;
        }

        .level-1 .level-label {
            padding-left: 20px;
            border-left: #dedede;
        }

        .level-2 .level-label {
            padding-left: 40px;
            border-left: #dedede;
        }

        .level-3 .level-label {
            padding-left: 60px;
            border-left: #dedede;
        }

        .level-4 .level-label {
            padding-left: 80px;
            border-left: #dedede;
        }

        .level-5 .level-label {
            padding-left: 90px;
            border-left: #dedede;
        }
    </style>
@endsection