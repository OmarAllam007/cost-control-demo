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
    <style>
        #main-panel{
            padding: 15px;
        }
        table {
            font-size: 16px;
        }
    </style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.js"></script>
@endsection

@section('body')
    <div class="row">
        <div class="panel panel-primary ">
            <div class="panel-heading">
                <h2 class="panel-title" style="font-size: x-large"><a href="#miles" data-toggle="collapse">Project Milestones</a></h2>
            </div>
            <div class="panel-body collapse" id="miles" >
                <br>
                <table class="table table-bordered " >
                    <thead>
                    <tr class="danger">
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
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-warning ">
            <div class="panel-heading">
                <h2 class="panel-title" style="font-size: x-large"><a href="#info" data-toggle="collapse">Project Cost Information</a></h2>
            </div>
            <div class="panel-body collapse" id="info">
                <br>
                <h2>Project Value: </h2>

                <table class="table table-bordered">
                    <thead>
                    <tr class="success">
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
                <br>
                <h2>Budget Information : </h2><br>
                <ul class="list-unstyled tree">
                    <li>
                        <h2><p>Revision 00</p></h2>
                        <article class="tree-item">
                            <table class="table table-bordered">
                                <thead>
                                <tr class="success">
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
                    </li>
                    <li>
                        <h2><p>Revision 01</p></h2>
                        <article>
                            <table class="table table-bordered">
                                <thead>
                                <tr class="success">
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
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-primary ">
            <div class="panel-heading">
                <h2 class="panel-title" style="font-size: x-large"><a href="#perf" data-toggle="collapse">Project Cost Performance</a></h2>
            </div>
            <div class="panel-body collapse" id="perf">
                <br>
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
                        @foreach($data as $key=>$value)
                            <td>{{number_format($value['actual_cost'],2)}}</td>
                            <td>{{number_format($value['allowable_cost'],2)}}</td>
                            <td @if(number_format($value['allowable_cost']-$value['actual_cost'],2) < 0 ) style="color: red;" @endif >{{number_format($value['allowable_cost']-$value['actual_cost'],2)}}</td>
                            <td style="@if($value['allowable_cost']/$value['actual_cost']<1) color: red; @endif">{{number_format($value['actual_cost']?$value['allowable_cost']/$value['actual_cost']:0,2)}}</td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection