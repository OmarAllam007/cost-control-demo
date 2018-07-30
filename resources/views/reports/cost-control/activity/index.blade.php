@extends('layouts.app')

@if(request('all'))
    @include('reports.all._standard-activity')
@endif

@section('title', 'Activity report')

@section('header')
    <h2 class="">{{$project->name}} - Activity report</h2>
    <div class="pull-right btn-toolbar">
        {{--<a href="?print=1" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i> Print</a>--}}

        <a href="?excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Excel</a>

        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')

    @include('reports.cost-control.activity._filters')

    <div class="horizontal-scroll">

        <table class="table table-bordered" id="activity-header">
        <thead>
        <tr class="bg-primary">
            <th>Activity</th>

            <th>Base Line</th>

            <th>Previous Cost</th>
            <th>Previous Allowable</th>
            <th>Previous Var</th>

            <th>To Date Cost</th>
            <th>Allowable (EV) Cost</th>
            <th>To Date Cost Var</th>

            <th>Remaining Cost</th>
            <th>At Completion Cost</th>
            <th>Cost Variance</th>
        </tr>
        <tr>
            <th>
                <div class="display-flex">
                    <span class="flex">Total</span>

                    <a href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                       data-data="{{json_encode([
                            'Total' => 'Total',
                            'Base Line' => number_format($tree->where('parent', '')->sum('budget_cost'), 2),
                            'Previous Cost' => number_format($tree->where('parent', '')->sum('prev_cost'), 2),
                            'Previous Allowable' => number_format($tree->where('parent', '')->sum('prev_allowable'), 2),
                            'Previous Var' => number_format($tree->where('parent', '')->sum('prev_cost_var'), 2),
                            'To Date Cost' => number_format($tree->where('parent', '')->sum('to_date_cost'), 2),
                            'Allowable (EV) Cost' => number_format($tree->where('parent', '')->sum('to_date_allowable'), 2),
                            'To Date Cost Var' => number_format($tree->where('parent', '')->sum('to_date_var'), 2),
                            'Remaining Cost' => number_format($tree->where('parent', '')->sum('remaining_cost'), 2),
                            'At Completion Cost' => number_format($tree->where('parent', '')->sum('completion_cost'), 2),
                            'Cost Variance' => number_format($tree->where('parent', '')->sum('completion_var'), 2),
                           ]) }}">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </a>
                </div>

            </th>

            <th>{{number_format($tree->where('parent', '')->sum('budget_cost'), 2)}}</th>

            <th>{{number_format($tree->where('parent', '')->sum('prev_cost'), 2)}}</th>
            <th>{{number_format($tree->where('parent', '')->sum('prev_allowable'), 2)}}</th>

            <th class="{{$tree->where('parent', '')->sum('prev_cost_var') < 0? 'text-danger' : 'text-success'}}">{{number_format($tree->where('parent', '')->sum('prev_cost_var'), 2)}}</th>

            <th>{{number_format($tree->where('parent', '')->sum('to_date_cost'), 2)}}</th>
            <th>{{number_format($tree->where('parent', '')->sum('to_date_allowable'), 2)}}</th>

            <th class="{{$tree->where('parent', '')->sum('to_date_var') < 0? 'text-danger' : 'text-success'}}">{{number_format($tree->where('parent', '')->sum('to_date_var'), 2)}}</th>

            <th>{{number_format($tree->where('parent', '')->sum('remaining_cost'), 2)}}</th>
            <th>{{number_format($tree->where('parent', '')->sum('completion_cost'), 2)}}</th>

            <th class="{{$tree->where('parent', '')->sum('completion_var') < 0? 'text-danger' : 'text-success'}}">{{number_format($tree->where('parent', '')->sum('completion_var'), 2)}}</th>
        </tr>
        </thead>
        </table>

        <div class="vertical-scroll">

            <table class="table table-bordered table-hover" id="activity-table">

                <tbody>

                    @foreach($tree->where('parent', '')->sortBy('name') as $key => $level)
                        @include('reports.cost-control.activity._wbs', ['depth' => 1])
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .horizontal-scroll {
            overflow-x: auto;
        }

        .vertical-scroll {
            overflow-y: auto;
            max-height: 550px;
            width: 1820px;
            padding-right: 16px;
        }

    #activity-table a, #activity-table a:hover, #activity-table a:focus, #activity-table a:active {
        font-weight: 700;
        text-decoration: none;
    }

    .level-2 td:first-child {
        padding-left: 20px
    }

    .level-3 td:first-child {
        padding-left: 40px
    }

    .level-4 td:first-child {
        padding-left: 60px
    }

    .level-5 td:first-child {
        padding-left: 80px
    }

    .level-6 td:first-child {
        padding-left: 90px
    }

    .bg-primary a {
        color: #fff;
    }

    .activity {
        background-color: #e7f1fc;
    }

    #activity-table > tbody > tr.resource.bg-primary td,
    #activity-table > tbody > tr.top-material-resource.bg-primary td,
    #activity-table > tbody > tr.top-material.bg-primary td,
    #activity-table > tbody > tr.bg-primary:hover,
    #activity-table > tbody > tr.info.bg-primary:hover,
    #activity-table > tbody > tr.success.bg-primary:hover,
    #activity-table > tbody > tr.info.bg-primary > td,
    #activity-table > tbody > tr.success.bg-primary > td,
    #activity-table > tbody > tr.activity.bg-primary > td {
        background-color: #3097D1;
        color: #fff;
    }
        #activity-table {
            border-top: none;
        }

        .table {
            margin-bottom: 0;
            width: auto;
        }

        .table > thead > tr > th:first-child, .table > tbody > tr > td:first-child{
            width: 300px;
            max-width: 300px;
            min-width: 300px;
        }

        .table > thead > tr > th, .table > tbody > tr > td{
            width: 150px;
            max-width: 150px;
            min-width: 150px;
        }
    </style>
@endsection

@section('javascript')
    <script>
        $(function() {
            function closeRows(rows) {
                rows.each(function() {
                    const link = $(this).find('a');
                    const target = '.' + link.data('target');
                    link.find('.fa').addClass('fa-plus-square-o').removeClass('fa-minus-square-o');
                    const rows = $(target).addClass('hidden');
                    closeRows(rows);
                });
            }
            const activityTable = $('#activity-table').on('click', 'a', function(){
                const _self = $(this);
                const target = '.' + _self.data('target');
                const rows = $(target).toggleClass('hidden');
                _self.toggleClass('open').find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                if (!_self.hasClass('open')) {
                    closeRows(rows);
                }

                return false;
            }).on('click', 'tbody tr', function () {
                if ($(this).hasClass('bg-primary')) {
                    $(this).removeClass('bg-primary');
                } else {
                    activityTable.find('tr').removeClass('bg-primary');
                    $(this).addClass('bg-primary');
                }
            });
        });
    </script>

    @include('reports.partials.concerns-modal', ['report_name' => 'Activity'])
@append