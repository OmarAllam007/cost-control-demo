@extends('layouts.' . (request()->has('print') ? 'print' : 'app'))

@section('title', 'Comparison Report')

@section('header')
    <div class="display-flex">
        <h4 class="flex">Comparison Report &mdash; {{$project->name}}</h4>

        @if (!request()->has('print'))
            <div>
                <a href="?excel" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> Export</a>
                <a href="?print=1&paint=survey" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print</a>
                <a href="{{route('project.show', $project)}}#Reports" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('body')
    <div class="horizontal-scroll">
    <table class="table table-bordered" id="report-head">
        <thead>
        <tr>
            <th style="min-width: 150px; max-width: 150px; width: 150px;" rowspan="2">WBS</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;" rowspan="2">Cost Account</th>
            <th style="min-width: 200px; max-width: 200px; width: 200px;" rowspan="2">Item Description</th>
            <th style="min-width: 75px; max-width: 75px; width: 75px" rowspan="2">Unit</th>

            <th colspan="3">BOQ Price</th>
            <th colspan="3">Dry Cost</th>
            <th colspan="4">Budget Cost</th>

            <th class="text-center" style="min-width: 100px; max-width: 100px; width: 100px;" rowspan="2">Revised BOQ</th>

            <th colspan="2">Comparison</th>
        </tr>
        <tr>
            {{-- BOQ Price --}}
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Price U.R.</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Qty</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">BOQ Price</th>

            {{-- Dry Cost --}}
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Dry U.R.</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Qty</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Dry Cost</th>

            {{-- Budget Cost --}}
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Budget Qty</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Eng Qty</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Budget U.R.</th>
            <th style="min-width: 100px; max-width: 100px; width: 100px;">Budget Cost</th>
            
            {{-- Comparison --}}
            <th style="min-width: 200px; max-width: 200px; width: 200px;">(Budget U.R. - Dry U.R.) * Budget Qty</th>
            <th style="min-width: 200px; max-width: 200px; width: 200px;">(Budget Qty - Dry Qty) * Budget U.R.</th>
        </tr>
        </thead>
    </table>
    <section class="vertical-scroll">
        <table class="table table-condensed table-bordered" id="report-body">
            <tbody>

            @foreach($tree as $wbs_level)
                @include('reports.budget.comparison._recursive', ['wbs_level' => $wbs_level, 'depth' => 0])
            @endforeach
            </tbody>
        </table>
    </section>
    </div>
@endsection

@section('javascript')
    <script>
        $('.open-level').click(function(e) {
            e.preventDefault();
            const target = $('.' + $(this).data('target'));

            if (target.hasClass('hidden')) {
                target.removeClass('hidden');
                $(this).find('i.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
            } else {
                closeRecursive(this);
            }
        });

        const rows = $('#report-body').find('tbody > tr');
        rows.click(function(e) {
            const isHighlighted = $(this).hasClass('highlighted');

            rows.removeClass('highlighted');
            if (!isHighlighted) {
                $(this).addClass('highlighted');
            }
        });

        function closeRecursive(elem) {
            const target = $('.' + $(elem).data('target'));
            target.addClass('hidden').each(function(){
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
            overflow-y: auto;
            padding-right: 20px;
            width: 2045px;
        }

        .horizontal-scroll {
            overflow-x: auto;
        }

        .horizontal-scroll .table {
            width: auto;
            margin-bottom: 0;
        }

        #report-body {
            min-width: 2026px;
        }

        #report-body tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-body tbody tr.highlighted > td {
            background-color: #ffc;
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
        }

        .level-2 .level-label {
            padding-left: 40px;
        }

        .level-3 .level-label {
            padding-left: 60px;
        }

        .level-4 .level-label {
            padding-left: 80px;
        }
    </style>
@endsection