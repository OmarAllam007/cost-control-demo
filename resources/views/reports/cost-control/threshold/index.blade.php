@extends('layouts.' . (request()->exists('print')? 'print' : 'app'))

@section('title', 'Cost threshold report')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{ $project->name }} &mdash; Cost threshold report</h2>
        <div class="text-right">
            <a href="?excel" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
            <a href="?print" class="btn btn-sm btn-info"><i class="fa fa-print"></i> Print</a>
            <a href="?print" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back to project</a>
        </div>
    </div>
@endsection

@section('body')
    <section class="horizontal-scroll">
        <div class="table-header">
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr class="bg-primary">
                        <th class="w-300">Wbs Level</th>
                        <th class="w-300">Activity</th>
                        <th class="w-200">Allowable Cost</th>
                        <th class="w-200">To Date Cost</th>
                        <th class="w-200">Variance</th>
                        <th class="w-200">Difference %</th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="table-body vertical-scroll">
            <table class="table table-bordered table-condensed" id="activitiesTable">
                <tbody>
                    @foreach ($tree  as $level)
                        @include('reports.cost-control.threshold.wbs_level', ['depth' => 0])
                    @endforeach                    
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('css')
    <style>
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

        .open-level, .open-level:active, .open-level:focus, .open-level:hover {
            text-decoration: none;
            font-weight: 700;
        }
        .w-300 {
            width: 300px;
            max-width: 300px;
            min-width: 300px;
        }

        .w-600 {
            width: 600px;
            max-width: 600px;
            min-width: 600px;
        }

        .w-200 {
            width: 200px;
            max-width: 200px;
            min-width: 200px;
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
            width: 1420px;
            overflow-y: scroll;
        }

        .horizontal-scroll {
            overflow-x: auto;
        }

        .highlight {

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

            $('#activitiesTable').on('click', '.open-level',function (e) {
                e.preventDefault();

                let selector = '.' + this.dataset.target;
                $(this).toggleClass('open').find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                let rows = $(selector).toggleClass('hidden');

                if (!$(this).hasClass('open')) {
                    closeRows(rows);
                }
            });

            const rows = $('#activitiesTable tr').click(function () {
                rows.removeClass('highlight');
                $(this).addClass('highlight').find('a').click();
            });
        });
    </script>
@append