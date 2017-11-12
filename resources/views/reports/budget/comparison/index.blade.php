@php $print = request()->exists('print') @endphp
@extends('layouts.' . ($print ? 'print' : 'app'))

@section('title', 'Comparison Report')

@section('header')
    <div class="display-flex">
        <h4 class="flex">Comparison Report &mdash; {{$project->name}}</h4>

        @if (!$print)
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
            <tr class="bg-primary">
                <th class="text-center" style="min-width: 150px; max-width: 150px; width: 150px;" rowspan="2">WBS</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;" rowspan="2">Item Code</th>
                <th class="text-center" style="min-width: 150px; max-width: 150px; width: 150px;" rowspan="2">Cost Account</th>
                <th class="text-center" style="min-width: 200px; max-width: 200px; width: 200px;" rowspan="2">Item Description</th>
                <th class="text-center" style="min-width: 75px; max-width: 75px; width: 75px" rowspan="2">Unit</th>

                <th class="text-center" colspan="3">Client BOQ</th>
                <th class="text-center" colspan="3">Dry Cost</th>
                <th class="text-center" colspan="4">Budget Cost</th>

                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;" rowspan="2">Revised BOQ</th>

                <th class="text-center" colspan="2">Comparison</th>
            </tr>
            <tr class="bg-primary">
                {{-- BOQ Price --}}
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Price U.R.</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Estimated Qty</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Total Amount</th>

                {{-- Dry Cost --}}
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Dry U.R.</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Qty</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Dry Cost</th>

                {{-- Budget Cost --}}
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Budget Qty</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Eng Qty</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Budget U.R.</th>
                <th class="text-center" style="min-width: 120px; max-width: 120px; width: 120px;">Budget Cost</th>

                {{-- Comparison --}}
                <th class="text-center" style="min-width: 200px; max-width: 200px; width: 200px;">(Budget U.R. - Dry U.R.) * Budget Qty</th>
                <th class="text-center" style="min-width: 200px; max-width: 200px; width: 200px;">(Budget Qty - Dry Qty) * Budget U.R.</th>
            </tr>
            <tr class="info">
                <th class="level-label text-strong" colspan="7" style="width: 100%">Total</th>

                <th style="width: 120px; min-width: 120px;  max-width: 120px;">{{number_format($tree->sum('boq_cost', 2), 2)}}</th>

                {{-- Dry --}}
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">&nbsp;</th>
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">&nbsp;</th>
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">{{number_format($tree->sum('dry_cost', 2), 2)}}</th>

                {{-- Budget --}}
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">&nbsp;</th>
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">&nbsp;</th>
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">&nbsp;</th>
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">{{number_format($tree->sum('cost', 2), 2)}}</th>

                {{-- Revised BOQ --}}
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">{{number_format($tree->sum('revised_boq', 2), 2)}}</th>

                {{-- Comparison --}}
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">{{number_format($tree->sum('price_diff', 2), 2)}}</th>
                <th style="width: 120px; min-width: 120px;  max-width: 120px;">{{number_format($tree->sum('qty_diff', 2), 2)}}</th>
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

        const rows = $('#report-body').find('tbody > tr');
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

        @if (!request('print'))
        .vertical-scroll {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 20px;
            width: 2435px;
        }

        .horizontal-scroll {
            overflow-x: auto;
            font-size: 12px;
        }

        .horizontal-scroll .table {
            width: 100%;
            margin-bottom: 0;
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

        @endif
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