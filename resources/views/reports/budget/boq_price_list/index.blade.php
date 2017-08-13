@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - BOQ PRICE LIST Report</h2>
    <div class="pull-right">
        <a href="?excel" class="btn btn-info btn-sm print">
            <i class="fa fa-cloud-download"></i> Excel
        </a>

        <a href="?print=1&paint=boq-price" target="_blank" class="btn btn-success btn-sm print">
            <i class="fa fa-print"></i> Print
        </a>

        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')
    <table class="table table-condensed table-bordered" id="report-table">
        <thead>
        <tr>
            <td>Item</td>
            <td>Cost Account</td>
            <td>Budget Qty</td>
            <td>U.O.M</td>
            <td>General Requirement</td>
            <td>Labours</td>
            <td>Material</td>
            <td>Subcontractors</td>
            <td>Equipment</td>
            <td>Scaffolding</td>
            <td>Others</td>
            <td>Grand Total</td>
        </tr>
        </thead>
        <tbody>
        @foreach ($tree as $wbs_level)
            @include('reports.budget.boq_price_list._recursive', ['depth' => 0, 'wbs_level' => $wbs_level])
        @endforeach
        </tbody>
    </table>
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

        $('#report-table tbody > tr').click(function(e) {
            const isHighlighted = $(this).hasClass('highlighted');

            $('#report-table tbody tr').removeClass('highlighted');
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

        #report-table tbody tr:hover > td {
            background-color: rgba(255, 255, 204, 0.7);
        }

        #report-table tbody tr.highlighted > td {
            background-color: #ffc;
        }

        .table-bordered > tbody > tr.level-0 td {
            background: #f7f7f7;
        }

        .table-bordered > tbody > tr.level-1 td:first-child,
        .table-bordered > tbody > tr.level-2 td:first-child,
        .table-bordered > tbody > tr.level-3 td:first-child {
            border-left: 1px solid #ddd;
        }

        .table-bordered > tbody > tr.level-1 td:last-child,
        .table-bordered > tbody > tr.level-2 td:last-child,
        .table-bordered > tbody > tr.level-3 td:last-child {
            border-right: 1px solid #ddd;
        }

        .table-bordered > tbody > tr.level-1 td {
            background: #ededed;
            border: 1px solid #fff;
        }

        .table-bordered > tbody > tr.level-2 td {
            background: #e6e6e6;
            border: 1px solid #fff;
        }

        .table-bordered > tbody > tr.level-3 td {
            background: #dedede;
            border: 1px solid #fff;
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