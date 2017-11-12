@extends('layouts.app')

@section('title', 'Waste Index')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Waste Index</h2>

        <div class="text-right">
            @php $excel_url = request()->getUri() . (request()->getQueryString()? '&' : '?') . 'excel'; @endphp
            <a href="{{$excel_url}}" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
            <a href="{{route('cost-man-days.import', $project)}}" class="btn btn-primary btn-sm"><i class="fa fa-cloud-upload"></i> Import man days</a>
            <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Back to Project
            </a>
        </div>
    </div>
@endsection

@section('body')
    @include('reports.cost-control.waste-index._filters')

    <div class="horizontal-scroll">
        <section class="table-header">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr class="bg-primary">
                    <th class="w-400">Activity</th>
                    <th class="w-150">Budget Unit</th>
                    <th class="w-150">Progress</th>
                    <th class="w-150">Sum of Earned Mandays</th>
                    <th class="w-150">Sum of Actual Mandays</th>
                    <th class="w-150">Sum of Variance</th>
                    <th class="w-150">P.I.</th>
                </tr>
                <tr class="bg-primary">
                    <th class="w-400">Total</th>
                    <th class="w-150">{{number_format($tree->sum('budget_unit'), 2)}}</th>
                    <th class="w-150"></th>
                    <th class="w-150">{{number_format($tree->sum('allowable_qty'), 2)}}</th>
                    <th class="w-150">{{number_format($tree->sum('actual_man_days'), 2)}}</th>
                    <th class="w-150 {{$tree->sum('variance') < 0 ? 'text-danger' : ''}}">{{number_format($tree->sum('variance'), 2)}}</th>
                    {{-- todo: Add average PI here --}}
                    <th class="w-150">%</th>
                </tr>
                </thead>
            </table>
        </section>

        <section class="vertical-scroll">
            <table class="table table-bordered table-condensed" id="resourcesTable">
                <tbody>
                @foreach($tree as $level)
                    @include('reports.cost-control.productivity-index.level', ['depth' => 0])
                @endforeach
                </tbody>
            </table>
        </section>
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

        .level-4 td.level-label {
            padding-left: 120px;
        }

        .level-5 td.level-label {
            padding-left: 140px;
        }

        .level-6 td.level-label {
            padding-left: 140px;
        }

        .open-level, .open-level:active, .open-level:focus, .open-level:hover {
            text-decoration: none;
            font-weight: 700;
        }
        .w-400 {
            width: 400px;
            max-width: 400px;
            min-width: 400px;
        }

        .w-1000 {
            width: 1000px;
            max-width: 1000px;
            min-width: 1000px;
        }

        .w-150 {
            width: 150px;
            max-width: 150px;
            min-width: 150px;
        }

        .table-header, .vertical-scroll {
            padding-right: 20px;
        }

        .table-header .table {
            margin-bottom: 0;
        }

        .table {
            width: auto;
        }

        .vertical-scroll {
            max-height: 500px;
            width: 1320px;
            overflow-y: scroll;
        }

        .horizontal-scroll {
            overflow-x: auto;
        }

        #ResourceTypeModal .modal-body {
            max-height: 400px;
            overflow-y: scroll;
        }

    </style>
@endsection

@section('javascript')
    <script>
        $(function () {
            function closeRows(rows) {
                rows.find('a').each(function () {
                    const selector = '.' + this.dataset.target;
                    const subrows = $(selector).addClass('hidden');
                    $('.fa', this).addClass('fa-plus-square-o').removeClass('fa-minus-square-o open');
                    closeRows(subrows);
                });
            }

            $('#resourcesTable').on('click', '.open-level',function (e) {
                e.preventDefault();

                let selector = '.' + this.dataset.target;
                $(this).toggleClass('open').find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                let rows = $(selector).toggleClass('hidden');

                if (!$(this).hasClass('open')) {
                    closeRows(rows);
                }
            });

            const rows = $('#resourcesTable tr').click(function () {
                rows.removeClass('highlight');
                $(this).addClass('highlight').find('a').click();
            });
        });
    </script>
@append