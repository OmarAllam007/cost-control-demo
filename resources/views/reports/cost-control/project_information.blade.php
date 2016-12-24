@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif
@section('header')
    <h2>Project Information</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
    <style>
        table {
            font-size: 16px;
        }
    </style>
@endsection

@section('body')
    <div class="blue-first-level">
        <h1>PROJECT DATES</h1>
    </div><br><br>
    <table class="table table-bordered">
        <thead>
        <th>Project Cost Account No.</th>
        <th>Project Name</th>
        <th>Original Start date</th>
        <th>Original Finish Date</th>
        <th>Expected Finish Date</th>
        <th>Time elappsed</th>
        <th>Time remaining</th>
        </thead>
        <tbody>
        <td>{{$project->project_code}}</td>
        <td>{{$project->name}}</td>
        <td>{{$project->project_start_date}}</td>
        <td>{{$project->original_finished_date}}</td>
        <td>{{$project->expected_finished_date}}</td>
        <td>{{(strtotime(date("Y-m-d"))-strtotime($project->project_start_date))/86400}} Day/s</td>
        <td>{{(strtotime($project->expected_finished_date)-strtotime(date("Y-m-d")))/86400>0?:0 }} Day/s</td>
        </tbody>
    </table>

    <div class="blue-second-level">
        <h1> PROJECT BUDGET COST DATA </h1>
    </div><br><br>
    <table class="table table-bordered">
        <thead>
        <th>Project Contract Signed Value</th>
        <th>Project Contract Budget Value</th>
        <th>Change Order Amount</th>
        <th>Direct Cost (Material & Labor & Subcon)</th>
        <th>Indirect Cost (General Requirement)</th>
        <th>Total Budget Cost</th>
        </thead>
        <tbody>
        <td>{{number_format($project->project_contract_signed_value,2)}}</td>
        <td>{{number_format($project->project_contract_budget_value,2)}}</td>
        <td>{{number_format($project->change_order_amount,2)}}</td>
        <td>{{number_format($project->direct_cost_material,2)}}</td>
        <td>{{number_format($project->indirect_cost_general,2)}}</td>
        <td>{{number_format($project->total_budget_cost,2)}}</td>
        </tbody>
    </table><br><br>
    <div class="blue-third-level">
        <h1> PROJECT BUDGET COST DATA </h1>
    </div><br><br>

    <table class="table table-bordered">
        <thead>
        <th>Allowable Cost</th>
        <th>Actual Cost</th>
        <th>Cost Performance Index</th>
        <th>Cost Variance</th>
        </thead>
        <tbody>
        @foreach($data as $key=>$value)
            <td>{{number_format($value['actual_cost'],2)}}</td>
            <td>{{number_format($value['allowable_cost'],2)}}</td>
            <td>{{number_format($value['actual_cost']?$value['allowable_cost']/$value['actual_cost']:0,2)}}</td>
            <td>{{number_format($value['allowable_cost']-$value['actual_cost'],2)}}</td>
        @endforeach
        </tbody>
    </table>

@endsection