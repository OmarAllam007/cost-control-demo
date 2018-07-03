@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('title', 'Cost Summary Report | ' . $project->name)

@section('header')
    <h2 id="report_name">{{$project->name}} &mdash; Cost Summary Report</h2>

    <div class="btn-toolbar pull-right">
        {{--<a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#AllModal">--}}
        {{--<i class="fa fa-warning"></i> Concerns--}}
        {{--</a>--}}

        <a href="?excel" class="btn btn-success btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Excel</a>

        {{--<a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>--}}

        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('body')

    <div class="row mb-3">
        <form action="" class="col-sm-6 col-md-4 display-flex" method="get">
            {{Form::select('period', $periods, $period->id,  ['placeholder' => 'Choose a Period', 'class'=>'form-control flex mr-10'])}}
            {{Form::submit('Submit', ['class'=>'btn btn-success'])}}
        </form>
    </div>

    <table class="table cost-summary-table">
        <thead>
        <tr class="bg-blue-light">
            <th class="col-sm-2" rowspan="2">Resource Type</th>
            <th class="text-center">Budget</th>
            <th class="text-center">Previous</th>
            <th class="text-center" colspan="3">To-Date</th>
            <th class="text-center" colspan="1">Remaining</th>
            <th class="text-center" colspan="3">At Completion</th>
        </tr>
        <tr class="bg-blue-lighter">
            <th class="col-xs-1">Base Line</th>
            <th class="col-xs-1">Previous Cost</th>
            <th class="col-xs-1">To Date Cost</th>
            <th class="col-xs-1">Allowable (EV) Cost</th>
            <th class="col-xs-1">To Date Cost Variance</th>
            <th class="col-xs-1">Remaining Cost</th>
            <th class="col-xs-1">At Completion Cost</th>
            <th class="col-xs-1">At Completion Cost Variance</th>
            {{--<th class="col-xs-1">Concern</th>--}}
        </tr>

        </thead>
        <tbody>
        @foreach($toDateData as $typeToDateData)
            <tr>
                <td>
                    <div class="display-flex">
                        <span class="flex">{{$typeToDateData->type}}</span>

                        <a  href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                            data-data="{{ json_encode(['Resource Type' => $typeToDateData->type, 'Base Line' => number_format($typeToDateData['budget_cost'], 2), 'Previous Cost' => number_format($typeToDateData['previous_cost'], 2), 'To Date Cost' => number_format($typeToDateData['to_date_cost'], 2), 'Allowable (EV) Cost' => number_format($typeToDateData['ev'], 2), 'To Date Cost Variance' => number_format($typeToDateData['to_date_var'], 2), 'Remaining Cost' => number_format($typeToDateData['remaining_cost'], 2), 'At Completion Cost' => number_format($typeToDateData['completion_cost'], 2), 'At Completion Cost Variance' => number_format($typeToDateData['completion_cost_var'], 2)]) }}">
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        </a>
                    </div>
                </td>
                <td>{{number_format($typeToDateData['budget_cost']??0,2) }}</td>
                <td>{{number_format($typeToDateData['previous_cost']??0,2)}}</td>
                <td>{{number_format($typeToDateData['to_date_cost']??0, 2)}}</td>
                <td>{{number_format($typeToDateData['ev']??0,2)}}</td>
                <td class="{{$typeToDateData['to_date_var'] > 0? 'text-success' : 'text-danger' }}">{{number_format($typeToDateData['to_date_var']??0,2)}}</td>
                <td>{{number_format($typeToDateData['remaining_cost']??0,2)}}</td>
                <td>{{number_format($typeToDateData['completion_cost']??0,2)}}</td>
                <td class="{{$typeToDateData['completion_cost_var'] > 0? 'text-success' : 'text-danger' }}">{{number_format($typeToDateData['completion_cost_var']??0,2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="bg-blue-light">
            <th>
                <div class="display-flex">
                    <span class="flex">Total</span>
                    <a  href="#" class="btn btn-warning btn-xs concern-btn" title="Add issue or concern"
                        data-data="{{ json_encode([
                            'Type' => 'Total', 'Base Line' => number_format($toDateData->sum('budget_cost'), 2), 'Previous Cost' => number_format($toDateData->sum('previous_cost'), 2),
                            'To Date Cost' => number_format($toDateData->sum('to_date_cost'), 2), 'Allowable (EV) Cost' => number_format($toDateData->sum('ev'), 2), 'To Date Cost Variance' => number_format($toDateData->sum('to_date_var'), 2),
                            'Remaining Cost' => number_format($toDateData->sum('remaining_cost'), 2), 'At Completion Cost' => number_format($toDateData->sum('completion_cost'), 2), 'At Completion Cost Variance' => number_format($toDateData->sum('completion_cost_var'), 2)]) }}">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </a>
                </div>
            </th>
            <th>{{number_format($toDateData->sum('budget_cost'), 2)}}</th>
            <th>{{number_format($toDateData->sum('previous_cost'), 2)}}</th>
            <th>{{number_format($toDateData->sum('to_date_cost'), 2)}}</th>
            <th>{{number_format($toDateData->sum('ev'), 2)}}</th>
            <th class="{{$toDateData->sum('to_date_var') > 0? 'text-success' : 'text-danger' }}">{{number_format($toDateData->sum('to_date_var'), 2)}}</th>
            <th>{{number_format($toDateData->sum('remaining_cost'), 2)}}</th>
            <th>{{number_format($toDateData->sum('completion_cost'), 2)}}</th>
            <th class="{{$toDateData->sum('completion_cost_var') > 0? 'text-success' : 'text-danger' }}">{{number_format($toDateData->sum('completion_cost_var'), 2)}}</th>
        </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-md-6">
            <h4 class="text-center">To date Cost vs Allowable Cost</h4>
            <div id="to_date_vs_allowable_chart"></div>
        </div>

        <div class="col-md-6">
            <h4 class="text-center">Budget Cost vs At Completion</h4>
            <div id="budget_cost_vs_completion_chart"></div>
        </div>

        <div class="col-md-6">
            <h4 class="text-center">Cost At Completion</h4>
            <div id="completion_cost_trend_chart"></div>
        </div>

        <div class="col-md-6">
            <h4 class="text-center">Variance At Completion</h4>
            <div id="completion_cost_var_trend_chart"></div>
        </div>

        <div class="col-md-12">
            <h4 class="text-center">Variance At Completion By Type Trend</h4>
            <div id="completion_cost_var_trend_by_type_chart"></div>
        </div>
    </div>

    @include('reports.partials.concerns-modal', ['report_name' => 'Cost Summary'])
    {{--@if(count($concerns))--}}
    {{--@include('reports._cost_summery_concerns')--}}
    {{--@endif--}}
@endsection

@section('javascript')
   <script src="{{asset('js/d3.min.js')}}"></script>
   <script src="{{asset('js/c3.min.js')}}"></script>

   <script>
       $(function() {
           const concernsModal = $('#concerns-modal').on('bs.modal-shown', function() {
               $(this).find('textarea').focus();
           }).on('click', '.send-concern', function(e) {
               e.preventDefault();
               $(this).find('i').removeClass('fa-check').addClass('fa-spinner fa-spin').end().prop('disabled', true);
               $.ajax({
                   url: concernsForm.attr('action'),
                   data: concernsForm.serialize(),
                   dataType: 'json',
                   method: 'post'
               }).then(() => {
                   concernsModal.modal('hide');
                   concernsForm.find('textarea').val('');
                   $(this).find('i').addClass('fa-check').removeClass('fa-spinner fa-spin').end().prop('disabled', false);
               }, () => {
                   $(this).find('i').addClass('fa-check').removeClass('fa-spinner fa-spin').end().prop('disabled', false);
                    // concernsModal.modal('hide');
               });
           });

           const concernsForm = concernsModal.find('form');

           const dataField = concernsModal.find('#concern-data');

           $('.concern-btn').on('click', function(e) {
               e.preventDefault();
               dataField.val(e.currentTarget.dataset.data);
               const data = JSON.parse(e.currentTarget.dataset.data);
               const header = concernsModal.find('thead tr');
               const body = concernsModal.find('tbody tr');

               header.find('th').remove();
               body.find('td').remove();

               for (const key in data) {
                   const value = data[key];
                   header.append($('<th>').text(key));
                   body.append($('<td>').text(value));
               }

               concernsModal.modal();
           });


       });
   </script>

    @include('reports.cost-control.cost-summary._charts', ['report_name' => 'Cost Summary'])
@endsection

@section('css')
    <link href="{{asset('css/c3.min.css')}}" rel="stylesheet"/>

    <style>
        .mb-3 {
            margin-bottom: 3rem;
        }

        table.table.cost-summary-table,
        table.table.cost-summary-table>thead>tr>th,
        table.table.cost-summary-table>tfoot>tr>th,
        table.table.cost-summary-table>tbody>tr>td {
            border: 2px solid #444;
            line-height: 25px;
        }

        .concern-btn {
            display: none;
        }

        tr:hover .concern-btn {
            display: inline;
        }
    </style>
@endsection