@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h2 class="">{{$project->name}} - BOQ Report</h2>
    <div class="pull-right">
        {{-- <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a> --}}
        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')

    @include('reports.cost-control.boq-report._filters')

    <div class="horizontal-scroll">
    <table class="table table-bordered" id="boq-header">
        <thead>
        <tr>
            <th class="cost-account-cell">Cost Account</th>
            <th class="label-cell">Description</th>
            <th class="number-cell">Dry Unit Price</th>
            <th class="number-cell">BOQ Unit Price</th>
            <th class="number-cell">Budget Unit Rate</th>
            <th class="number-cell">BOQ Qty</th>
            <th class="number-cell">Budget Qty</th>
            <th class="number-cell">Physical Qty</th>
            <th class="number-cell">Dry Cost</th>
            <th class="number-cell">BOQ Cost</th>
            <th class="number-cell">Base line</th>
            <th class="number-cell">To Date Cost</th>
            <th class="number-cell">To Date Allowable</th>
            <th class="number-cell">To Date Cost Var</th>
            <th class="number-cell">Remaining Cost</th>
            <th class="number-cell">At Completion Cost</th>
            <th class="number-cell">At Completion Var</th>
        </tr>
        </thead>
    </table>

        <div class="vertical-scroll">
    <table class="table table-bordered table-hover" id="boq-table">
            <tbody>
                @foreach($tree->where('parent', '')->sortBy('name') as $key => $level)
                    @include('reports.cost-control.boq-report._wbs', ['depth' => 1])
                @endforeach
            </tbody>
    </table>
        </div>
    </div>

@endsection

@section('css')
    <style>
        .horizontal-scroll {overflow-x: auto}
        .vertical-scroll {max-height:550px; overflow-y: auto; width: 2870px; padding-right: 16px;}
        .table {margin-bottom: 0; width: auto;}
        .label-cell { width: 300px; min-width: 300px; max-width: 300px; }
        .cost-account-cell { width: 300px; min-width: 300px; max-width: 300px; }
        .number-cell { width: 150px; min-width: 150px; max-width: 150px; }
        .level-2 td:first-child { padding-left: 20px; }
        .level-3 td:first-child { padding-left: 40px; }
        .level-4 td:first-child { padding-left: 60px; }
        .level-5 td:first-child { padding-left: 80px; }
        .level-6 td:first-child { padding-left: 100px; }
        #boq-table {margin-top: -1px;}
        #boq-table a, #boq-table a:active, #boq-table a:focus, #boq-table a:hover {font-weight: 700; text-decoration: none;}

        #boq-table > tbody > tr.resource.bg-primary td,
        #boq-table > tbody > tr.top-material-resource.bg-primary td,
        #boq-table > tbody > tr.top-material.bg-primary td,
        #boq-table > tbody > tr.bg-primary:hover,
        #boq-table > tbody > tr.info.bg-primary:hover,
        #boq-table > tbody > tr.success.bg-primary:hover,
        #boq-table > tbody > tr.info.bg-primary > td,
        #boq-table > tbody > tr.success.bg-primary > td,
        #boq-table > tbody > tr.boq.bg-primary > td {
            background-color: #3097D1;
            color: #fff;
        }

        .bg-primary a {
            color: #fff;
        }
    </style>
@endsection

@section('javascript')
    <script>
        function closeRows(rows) {
            rows.each(function() {
                const link = $(this).find('a');
                const target = '.' + link.data('target');
                link.find('.fa').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                const rows = $(target).addClass('hidden');
                closeRows(rows);
            });
        }
        const boqTable = $('#boq-table').on('click', 'a', function(){
            const _self = $(this);
            const target = '.' + _self.data('target');
            const rows = $(target).toggleClass('hidden');
            _self.toggleClass('open').find('.fa').toggleClass('fa-plus-circle fa-minus-circle');
            if (!_self.hasClass('open')) {
                closeRows(rows);
            }

            return false;
        }).on('click', 'tbody tr', function () {
            if ($(this).hasClass('bg-primary')) {
                $(this).removeClass('bg-primary');
            } else {
                boqTable.find('tr').removeClass('bg-primary');
                $(this).addClass('bg-primary');
            }
        });
    </script>
@endsection