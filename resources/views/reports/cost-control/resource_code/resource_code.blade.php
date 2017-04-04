@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Resource Dictionary Report</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>--}}
        {{--Print</a>--}}
        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <div class="fixed-table-container">
    <table class="table table-bordered" id="resourcesTable">
        <thead>
        <tr>
            <th width="300" rowspan="2"><div class="th-inner">Resource</div></th>
            <th colspan="3"><div class="th-inner">Budget</div></th>
            <th colspan="3"><div class="th-inner">Previous</div></th>
            <th colspan="3"><div class="th-inner">Current</div></th>
            <th colspan="7"><div class="th-inner">To Date</div></th>
            <th colspan="3"><div class="th-inner">Remaining</div></th>
            <th colspan="6"><div class="th-inner">At Completion</div></th>
        </tr>
        <tr>
            {{-- Budget --}}
            <th width="200"><div class="th-inner">U.Price</div></th>
            <th width="200"><div class="th-inner">Qty</div></th>
            <th width="200"><div class="th-inner">Cost</div></th>

            {{-- Previous --}}
            <th width="200"><div class="th-inner">U.Price</div></th>
            <th width="200"><div class="th-inner">Qty</div></th>
            <th width="200"><div class="th-inner">Cost</div></th>

            {{-- Current --}}
            <th width="200"><div class="th-inner">U.Price</div></th>
            <th width="200"><div class="th-inner">Qty</div></th>
            <th width="200"><div class="th-inner">Cost</div></th>

            {{-- To Date --}}
            <th width="200"><div class="th-inner">U.Price</div></th>
            <th width="200"><div class="th-inner">Qty</div></th>
            <th width="200"><div class="th-inner">Allowable Qty</div></th>
            <th width="200"><div class="th-inner">Qty Var</div></th>
            <th width="200"><div class="th-inner">Cost</div></th>
            <th width="200"><div class="th-inner">Allowable Cost</div></th>
            <th width="200"><div class="th-inner">Cost Var</div></th>

            {{-- Remaining --}}
            <th width="200"><div class="th-inner">U.Price</div></th>
            <th width="200"><div class="th-inner">Qty</div></th>
            <th width="200"><div class="th-inner">Cost</div></th>

            {{-- At Completion --}}
            <th width="200"><div class="th-inner">U.Price</div></th>
            <th width="200"><div class="th-inner">Qty</div></th>
            <th width="200"><div class="th-inner">Qty Var</div></th>
            <th width="200"><div class="th-inner">Cost</div></th>
            <th width="200"><div class="th-inner">Cost Var</div></th>
            <th width="150"><div class="th-inner">P/W Index</div></th>
        </tr>
        </thead>
        <tbody>
        @foreach($tree as $name => $typeData)
            @include('reports.cost-control.resource_code._type')
        @endforeach
        </tbody>
    </table>
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
            width: auto;
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

            $('#resourcesTable').on('click', 'a', function() {
                const target = $(this).data('target');
                const rows = $(target).toggleClass('hidden');
                $(this).toggleClass('open').find('.fa').toggleClass('fa-plus-square-o fa-minus-square-o');
                closeRows(rows);
                return false;
            });
        });
    </script>
@endsection