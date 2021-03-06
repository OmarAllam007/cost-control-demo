@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif

@section('title', 'Resource Dictionary Report')

@section('header')
    <h2 class="">{{$project->name}} - Resource Dictionary Report</h2>
    <div class="pull-right btn-toolbar">
        {{--<a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>--}}
        {{--Print</a>--}}
        <a href="?excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Excel</a>
        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
        </div>
    @endsection

    @section('body')
        @include('reports.cost-control.resource_code._filters')

        <div class="fixed-table-container">

                <table class="table table-bordered  resources-table">
                    <thead>
                    <tr class="bg-primary thead-top">
                        <th class="resource-cell right-border" rowspan="2">Resource</th>
                        <th class="text-center right-border" colspan="3">Budget</th>
                        <th class="text-center right-border" colspan="3">Previous</th>
                        <th class="text-center right-border" colspan="3">Current</th>
                        <th class="text-center right-border" colspan="7">To Date</th>
                        <th class="text-center right-border" colspan="3">Remaining</th>
                        <th class="text-center" colspan="6">At Completion</th>
                    </tr>
                    <tr class="bg-primary thead-bottom">
                        {{-- Budget --}}
                    <th class="text-center number-cell" width="100">U.Price</th>
                    <th class="text-center number-cell" width="100">Qty</th>
                    <th class="text-center number-cell right-border" width="100">Cost</th>

                    {{-- Previous --}}
                    <th class="text-center number-cell" width="100">U.Price</th>
                    <th class="text-center number-cell" width="100">Qty</th>
                    <th class="text-center number-cell right-border" width="100">Cost</th>

                    {{-- Current --}}
                    <th class="text-center number-cell" width="100">U.Price</th>
                    <th class="text-center number-cell" width="100">Qty</th>
                    <th class="text-center number-cell right-border" width="100">Cost</th>

                    {{-- To Date --}}
                    <th class="text-center number-cell" width="100">U.Price</th>
                    <th class="text-center number-cell" width="100">Qty</th>
                    <th class="text-center number-cell" width="100">Allowable Qty</th>
                    <th class="text-center number-cell" width="100">Qty Var</th>
                    <th class="text-center number-cell" width="100">Cost</th>
                    <th class="text-center number-cell" width="100">Allowable Cost</th>
                    <th class="text-center number-cell right-border" width="100">Cost Var</th>

                    {{-- Remaining --}}
                    <th class="text-center number-cell" width="100">U.Price</th>
                    <th class="text-center number-cell" width="100">Qty</th>
                    <th class="text-center number-cell right-border" width="100">Cost</th>

                    {{-- At Completion --}}
                    <th class="text-center number-cell" width="100">U.Price</th>
                    <th class="text-center number-cell" width="100">Qty</th>
                    <th class="text-center number-cell" width="100">Qty Var</th>
                    <th class="text-center number-cell" width="100">Cost</th>
                    <th class="text-center number-cell" width="100">Cost Var</th>
                    <th class="text-center number-cell" width="100">P/W Index</th>
                </tr>
                </thead>
            </table>

        <div class="fixed-table-container-inner">
            <table class="table table-bordered table-hover resources-table" id="resourcesTable">
                <tbody>
                @foreach($tree as $type_id => $type)
                    @include('reports.cost-control.resource_code._type', ['depth' => 0])
                @endforeach
                </tbody>
            </table>
     </div>
    </div>
@endsection

@section('css')
    <style>
        .discipline td:first-child {
            padding-left: 30px;
        }

        .top-material td:first-child,.resource td:first-child{
            padding-left: 60px;
        }

        .top-material-resource td:first-child{
            padding-left: 80px;
        }

        .fixed-table-container {
            overflow-x: auto;
        }

        .fixed-table-container-inner {
            width: 3320px;
            overflow-y: auto;
            max-height: 600px;
        }

        .resource-cell {
            width: 300px;
            min-width: 300px;
            max-width: 300px;
        }

        .number-cell {
            width: 120px;
            min-width: 120px;
            max-width: 120px;
        }

        .table > tbody > tr > td.right-border, .table > thead > tr > th.right-border{
            border-right: 2px solid #999;
        }


        #resourcesTable a, #resourcesTable a:active, #resourcesTable a:focus {
            text-decoration: none;
        }

        .resources-table > thead > tr > th {
            border-bottom: none;
        }

        .resources-table {
            border: 2px solid #999;
            margin-bottom: 0;
            width: auto;
        }

        #resourcesTable {
            border-top: none;
        }

        .resource,.top-material-resource {
            background-color: #e7f1fc;
        }

        .top-material {
            background-color: #fa8840;
        }

        #resourcesTable > tbody > tr.top-material:hover {
            background-color: #F9690E;
        }

        #resourcesTable > tbody > tr.resource.bg-primary td,
        #resourcesTable > tbody > tr.top-material-resource.bg-primary td,
        #resourcesTable > tbody > tr.top-material.bg-primary td,
        #resourcesTable > tbody > tr.bg-primary:hover,
        #resourcesTable > tbody > tr.info.bg-primary:hover,
        #resourcesTable > tbody > tr.info.bg-primary > td {
            background-color: #3097D1;
        }

        .top-material td, .top-material td a, .bg-primary a {
            color: #fff;
        }

        .level-1 td.level-label, .level-1 th.level-label {
            padding-left: 30px;
        }

        .level-2 td.level-label, .level-2 th.level-label {
            padding-left: 60px;
        }

        .level-3 td.level-label, .level-3 th.level-label {
            padding-left: 90px;
        }

        .level-4 td.level-label, .level-4 th.level-label {
            padding-left: 120px;
        }

        .level-5 td.level-label, .level-5 th.level-label {
            padding-left: 140px;
        }

        .level-6 td.level-label, .level-6 th.level-label {
            padding-left: 140px;
        }
    </style>
@endsection

@section('javascript')
    <script>

        $(function() {
            function closeRows(rows) {
                rows.each(function() {
                    const link = $(this).find('a');
                    const target = link.data('target');
                    link.find('.fa').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                    const subRows = $(target).each(function() {
                        $(this).addClass('hidden').removeClass('open');
                    });
                    closeRows(subRows);
                });
            }

            const resourcesTable = $('#resourcesTable').on('click', 'a', function() {
                const target = $(this).data('target');
                const rows = $(target).toggleClass('hidden');
                $(this).toggleClass('open').find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                if (!$(this).hasClass('open')) {
                    closeRows(rows);
                }
                return false;
            }).on('click', 'tr', function() {
                if ($(this).hasClass('bg-primary')) {
                    $(this).removeClass('bg-primary')
                } else {
                    resourcesTable.find('tr').removeClass('bg-primary');
                    $(this).addClass('bg-primary')
                }
            });
        });
    </script>

    @include('reports.partials.concerns-modal', ['report_name' => 'Resource Dictionary'])
@endsection