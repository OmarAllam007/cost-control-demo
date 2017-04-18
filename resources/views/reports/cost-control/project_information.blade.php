@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif
@section('header')
    <h2>{{$project->name}} - Project Information Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')

    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost_control.info',$project)}}" class="form-inline col col-md-8" method="get">
            {{Form::select('period', $periods, session('period_id_'.$project->id), ['class'=>'form-control padding'])}}
            <button class="btn btn-primary btn-rounded"><i class="fa fa-check"></i> Submit</button>
        </form>
        <br>
    </div>

    <section id="milestones" class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title">Project Milestones</h2>
        </div>

        <table class="table table-bordered ">
            <thead>
            <tr class="active">
                <th>Project Cost Center</th>
                <th>Project Name</th>
                <th>Original Start date</th>
                <th>Original Finish Date</th>
                <th>Time elappsed</th>
                <th>Expected Finish Date</th>
                <th>Time remaining</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{$project->project_code}}</td>
                <td>{{$project->name}}</td>
                <td>{{$project->project_start_date}}</td>
                <td>{{$project->original_finished_date}}</td>
                <td>@if($project->project_start_date!=0) {{(strtotime(date("Y-m-d"))-strtotime($project->project_start_date))/86400}} @else
                        0 @endif Day/s
                </td>
                <td>{{$project->expected_finished_date}}</td>
                <td>@if($project->expected_finished_date!=0) {{(strtotime($project->expected_finished_date)-strtotime(date("Y-m-d")))/86400 }} @else
                        0 @endif Day/s
                </td>
            </tr>
            </tbody>
        </table>
    </section>

    <section id="cost-info" class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title">Project Cost Information</h2>
        </div>

        <div class="panel-body">
        <article class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><em>Project Value:</em></h3>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr class="active">
                    <th>Project Contract Amount</th>
                    <th>Project Dry Cost</th>
                    <th>Change Order Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{number_format((float)$project->project_contract_signed_value ?? 0 ,2)}}</td>
                    <td>{{number_format((float)$project->project_contract_budget_value ?? 0 ,2)}}</td>
                    <td>{{number_format((float)$project->change_order_amount ?? 0 ,2)}}</td>
                </tr>
                </tbody>
            </table>
        </article>

            <h4 class="page-header"><em>Budget Information:</em></h4>
            <article class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">Revision 00</h4>
                </div>

                <table class="table table-bordered">
                    <thead>
                    <tr class="active">
                        <th>Direct Cost (Material & Labor & Subcon)</th>
                        <th>Indirect Cost (General Requirement)</th>
                        <th>Total Budget Cost</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{number_format((float)$project->direct_cost_material ?? 0 ,2)}}</td>
                        <td>{{number_format((float)$project->indirect_cost_general ?? 0 ,2)}}</td>
                        <td>{{number_format((float)$project->total_budget_cost ?? 0 ,2)}}</td>
                    </tr>
                    </tbody>
                </table>
            </article>

            <article class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">Revision 01</h4>
                </div>

                <table class="table table-bordered">
                    <thead>
                    <tr class="active">
                        <th>Direct Cost (Material & Labor & Subcon)</th>
                        <th>Indirect Cost (General Requirement)</th>
                        <th>Total Budget Cost</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{number_format((float)$project->direct_cost_material ?? 0 ,2)}}</td>
                        <td>{{number_format((float)$project->indirect_cost_general ?? 0 ,2)}}</td>
                        <td>{{number_format((float)$project->total_budget_cost ?? 0 ,2)}}</td>
                    </tr>
                    </tbody>
                </table>
            </article>
        </div>
    </section>

    <section id="cost-performance" class="panel panel-primary">
        <div class="panel-heading">
            <h2 class="panel-title">Project Cost Performance</h2>
        </div>

        <table class="table table-bordered">
            <thead>
            <tr class="active">
                <th>Allowable Cost</th>
                <th>Actual Cost</th>
                <th>Cost Variance</th>
                <th>Cost Performance Index</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><strong>{{number_format($data['allowable_cost'],2)}}</strong></td>
                <td><strong>{{number_format($data['to_date_cost'],2)}}</strong></td>
                <td class="{{$data['cost_var'] < 0? 'text-danger' : 'text-success'}}"><strong>{{number_format($data['cost_var'],2)}}</strong></td>
                <td class="{{$data['cpi'] < 1? 'text-danger' : 'text-success'}}"><strong>{{number_format($data['cpi'],2)}}</strong></td>
            </tr>
            </tbody>
        </table>
    </section>
@endsection