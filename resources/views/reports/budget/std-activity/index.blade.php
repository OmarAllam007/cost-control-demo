@extends('layouts.' . (request()->has('print') ? 'print' : 'app'))

@section('title', 'Std Activity Cost')

@section('header')
    <div class="display-flex">
        <h4 class="flex">Std Activity Cost &mdash; {{$project->name}}</h4>

        @if (!request()->has('print'))
            <div>
                <a href="?excel" class="btn btn-sm btn-info"><i class="fa fa-cloud-download"></i> Export</a>
                <a href="?print=1&paint=std-activity" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Print</a>
                <a href="{{route('project.show', $project)}}#Reports" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
            </div>
        @endif
    </div>
@endsection

@section('image')
    <img src="{{asset('images/reports/standard-activity.jpg')}}" height="80%">
@endsection

@section('body')
    <table class="table table-condensed table-bordered" id="report-head">
        <thead>
        <tr class="bg-primary">
            <th class="col-sm-8">Activity</th>
            @if ($includeCost)
                <th class="col-sm-2">Budget Cost</th>
                <th class="col-sm-2">Weight (%)</th>
            @endif
        </tr>
        @if ($includeCost)
            <tr class="info">
                <th>Total</th>
                <th>{{number_format($tree->sum('cost'), 2)}}</th>
                <th>{{number_format($tree->sum('weight'), 2)}}%</th>
            </tr>
        @endif
        </thead>
    </table>
    <section class="vertical-scroll">
        <table class="table table-condensed table-bordered" id="report-body">
            <tbody>

            @foreach($tree as $division)
                @include('reports.budget.std-activity._recursive', ['division' => $division, 'depth' => 0])
            @endforeach
            </tbody>
        </table>
    </section>
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

        const rows = $('#report-table').find('tbody > tr');
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
            background: hsl(0, 0%, 97%);
        }

        .level-1 td {
            background: hsl(0, 0%, 93%);
        }

        .level-2 td {
            background: hsl(0, 0%, 90%);
        }

        .level-3 td {
            background: hsl(0, 0%, 87%);
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