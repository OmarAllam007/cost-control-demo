@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} - Overdraft</h2>
        <div class="btn-toolbar">
            {{--<a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>--}}
            {{--Print</a>--}}
            @can('actual_resources', $project)
                <a href="/project/{{$project->id}}/actual-revenue" class="btn btn-primary btn-sm"><i class="fa fa-cloud-upload"></i> Import Actual Revenue</a>
            @endcan

            <a href="?excel" class="btn btn-success btn-sm"><i class="fa fa-cloud-download"></i> Excel</a>

            <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        </div>
    </div>

@endsection
@section('body')

    <div class="horizontal-scroll">
        <table class="table table-bordered table-striped" id="reportHead">
            <thead>
            <tr>
                <th class="boq-cell">BOQ Description</th>
                <th class="price-cell">BOQ Estimated Qty</th>
                <th class="price-cell">BOQ Unit Price</th>
                <th class="price-cell">Physical Unit</th>
                <th class="price-cell">Physical Unit Excl. Unit Price Var</th>
                <th class="price-cell">Physical Revenue</th>
                <th class="price-cell">Physical Revenue Excl. Unit Price Var</th>
                <th class="price-cell">Actual Revenue</th>
                <th class="price-cell">Variance </th>
                <th class="price-cell">Variance Excl. Unit Price Var</th>
            </tr>
            <tr class="info">
                <th class="boq-cell">Total</th>
                <th class="price-cell"></th>
                <th class="price-cell"></th>
                <th class="price-cell"></th>
                <th class="price-cell"></th>
                <td class="price-cell">{{number_format($totals->physical_revenue, 2)}}</td>
                <td class="price-cell">{{number_format($totals->physical_revenue_upv, 2)}}</td>
                <td class="price-cell">{{number_format($totals->actual_revenue, 2)}}</td>
                <td class="price-cell {{$totals->var < 0? 'text-danger' : 'text-success'}}">{{number_format($totals->var, 2)}}</td>
                <td class="price-cell {{$totals->var_upv < 0? 'text-danger' : 'text-success'}}">{{number_format($totals->var_upv, 2)}}</td>
            </tr>
            </thead>
        </table>
        <div class="vertical-scroll">
            <table class="table table-bordered table-striped" id="reportData">
                <tbody>
                @foreach($tree as $level)
                    @include('reports.cost-control.over-draft._level')
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('head')
    <style>
        .level-1 td:first-child {
            padding-left: 20px;
            font-weight: 700;
        }

        .level-2 td:first-child{
            padding-left: 40px;
            font-weight: 700;
        }

        .level-3 td:first-child{
            padding-left: 60px;
            font-weight: 700;
        }

        .level-4 td:first-child{
            padding-left: 80px;
            font-weight: 700;
        }

        .level-5 td:first-child{
            padding-left: 90px;
            font-weight: 700;
        }

        .level-6 td:first-child, .level-7 td:first-child, .level-8 td:first-child{
            padding-left: 100px;
            font-weight: 700;
        }

        .horizontal-scroll {
            overflow-x: auto;
        }

        .vertical-scroll {
            max-height: 500px;
            overflow-x: auto;
            width: 2220px;
        }

        #reportHead, #reportData {
            margin-bottom: 0;
            width: auto;
        }

        #reportData a:hover, #reportData a:visited, #reportData a:active, #reportData a:focus{
            text-decoration: none;
        }

        .boq-cell {
            width: 400px;
            min-width: 400px;
            max-width: 400px;
        }

        .price-cell {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
        }

        #reportData.table > tbody > tr.highlight > td,
        #reportData.table > tbody > tr.success.highlight > td,
        #reportData.table > tbody > tr:hover > td,
        #reportData.table > tbody > tr.success:hover > td {
            background-color: #ffc;
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
                    link.find('.fa').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                    const subRows = $(target).each(function() {
                        $(this).addClass('hidden').removeClass('open');
                    });
                    closeRows(subRows);
                });
            }

            const reportData = $('#reportData').on('click', 'a', function() {
                const target = $(this).data('target');
                const rows = $(target).toggleClass('hidden');
                $(this).toggleClass('open').find('.fa').toggleClass('fa-plus-circle fa-minus-circle');
                if (!$(this).hasClass('open')) {
                    closeRows(rows);
                }
                return false;
            }).on('click', 'tr', function() {
                if ($(this).hasClass('highlight')) {
                    $(this).removeClass('highlight')
                } else {
                    reportData.find('tr').removeClass('highlight');
                    $(this).addClass('highlight')
                }
            });
        });
    </script>
@endsection