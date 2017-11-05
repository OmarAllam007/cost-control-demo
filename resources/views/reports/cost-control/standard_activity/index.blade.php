@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif

@section('title', 'Standard Activity Report | ' . $project->name)

@section('header')
    <h2 class="">{{$project->name}} - Standard Activity</h2>
    <div class="pull-right">
        {{--<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#AllModal">--}}
        {{--<i class="fa fa-warning"></i> Concerns--}}
        {{--</button>--}}
        {{--<a href="?print=1" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i> Print</a>--}}

        <a href="?excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Excel</a>

        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')

    @include('reports.cost-control.standard_activity._filters')

    <div class="horizontal-scroll">
        <div class="scroll-head">
        <table class="table table-bordered table-hover activity-table">
            <thead>
            <tr class="bg-primary">
                <th class="col-xs-2">Activity</th>
                <th class="col-xs-1">Budget Cost</th>
                <th class="col-xs-1">Previous Cost</th>
                <th class="col-xs-1">Previous Allowable</th>
                <th class="col-xs-1">Previous Var</th>
                <th class="col-xs-1">To Date Cost</th>
                <th class="col-xs-1">Allowable (EV) Cost</th>
                <th class="col-xs-1">To Date Variance</th>
                <th class="col-xs-1">Remaining Cost</th>
                <th class="col-xs-1">At Completion Cost</th>
                <th class="col-xs-1">Cost Variance</th>
            </tr>
            <tr>
                <th class="col-xs-2">Total</th>
                <th class="col-xs-1">{{number_format($currentTotals['budget_cost']??0,2) }}</th>
                <th class="col-xs-1">{{number_format($previousTotals['previous_cost']??0,2)}}</th>
                <th class="col-xs-1">{{number_format($previousTotals['previous_allowable']??0,2)}}</th>
                <th class="col-xs-1 {{$previousTotals['previous_var'] > 0? 'text-success' : 'text-danger'}}">{{number_format($previousTotals['previous_var']??0,2)}}</th>
                <th class="col-xs-1">{{number_format($currentTotals['to_date_cost']?? 0,2)}}</th>
                <th class="col-xs-1">{{number_format($currentTotals['to_date_allowable']??0,2)}}</th>
                <th class="col-xs-1 {{$currentTotals->to_date_var > 0? 'text-success' : 'text-danger'}}">{{number_format($currentTotals['to_date_var']??0,2)}}</th>
                <th class="col-xs-1">{{number_format($currentTotals['remaining']??0,2)}}</th>
                <th class="col-xs-1">{{number_format($currentTotals['at_completion_cost']??0,2)}}</th>
                <th class="col-xs-1 {{$currentTotals->cost_var > 0? 'text-success' : 'text-danger'}}">{{number_format($currentTotals->cost_var??0,2)}}</th>
            </tr>
            </thead>
        </table>
        </div>
        <div class="vertical-scroll">
            <table class="table table-bordered table-hover" id="activity-table">
                <tbody>
                @foreach($tree->where('index', 0) as $name => $level)
                    @include('reports.cost-control.standard_activity._recursive_report')
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection

@section('css')
    <style>

        .level-1 td.level-label {
            padding-left: 30px;
        }

        .level-2 td.level-label {
            padding-left: 60px;
        }

        .level-3 td.level-label {
            padding-left: 90px;
        }

        .level-activity {
            background: #e7f1fc;
        }

        .open-level, .open-level:active, .open-level:focus, .open-level:hover {
            text-decoration: none;
            font-weight: 700;
        }

        tr {
            cursor: pointer;
        }

        #activity-table {
            border-top: none;
        }

        .table {
            margin-bottom: 0;
            min-width: 100%;
        }

        .horizontal-scroll {
            overflow-x: auto;
        }

        .vertical-scroll {
            overflow-y: scroll;
            max-height: 550px;

        }

        .scroll-head {
            padding-right: 16px;
        }

    </style>
@endsection


@section('javascript')
    <script>
        $(function () {
            function closeRows(rows) {
                rows.find('a').each(function () {
                    const selector = '.' + $(this).data('target');
                    const rows = $(selector).addClass('hidden');
                    $(this).find('.fa').addClass('fa-plus-square-o').removeClass('fa-minus-square-o open');
                    closeRows(rows);
                });
            }

            $('#activity-table').on('click', '.open-level',function (e) {
                let selector = '.' + $(this).data('target');
                $(this).toggleClass('open').find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                let rows = $(selector).toggleClass('hidden');
                if (!$(this).hasClass('open')) {
                    closeRows(rows);
                }

                return false;
            });

            const rows = $('.activity-table tr').click(function () {
                rows.removeClass('info');
                $(this).addClass('info').find('a').click();
            });
        });
    </script>
@append
