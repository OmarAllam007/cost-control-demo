@extends('layouts.' . (request()->has('print') ? 'print' : 'app'))

@section('title', 'QS Summary')

@section('header')
    <div class="display-flex">
        <h4 class="flex">QS Summary &mdash; {{$project->name}}</h4>

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
    <table class="table table-condensed table-bordered" id="report-head">
        <thead>
        <tr class="bg-primary">
            <th class="col-sm-3">Activity</th>
            <th class="col-sm-2">Cost Account</th>
            <th class="col-sm-3">BOQ Description</th>
            <th class="col-sm-1">Eng Qty</th>
            <th class="col-sm-1">Budget Qty</th>
            <th class="col-sm-2">Unit of measure</th>
        </tr>
        </thead>
    </table>
    <section class="vertical-scroll">
        <table class="table table-condensed table-bordered" id="report-body">
            <tbody>

            @foreach($tree as $wbs_level)
                @include('reports.budget.qs-summary._recursive', ['wbs_level' => $wbs_level, 'depth' => 0])
            @endforeach
            </tbody>
        </table>
    </section>
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

        const rows = $('#report-table').find('tbody > tr');
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
            overflow-x: auto;
        }

        .table {
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

        .level-5 .level-label {
            padding-left: 100px;
        }

        .level-6 .level-label {
            padding-left: 110px;
        }

        .level-7 .level-label {
            padding-left: 120px;
        }
    </style>
@endsection