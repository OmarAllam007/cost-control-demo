@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.js"></script>

    <h2>{{$project->name}} - Cost Summery Report</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>

        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>

@endsection
@section('body')

    <div class="col col-md-8">
        <form action="{{route('cost_control.cost-summery',$project)}}" class="form-inline" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') , null, ['placeholder' => 'Choose a Period','class'=>'form-control'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>
    </div>
    <table class="table table-condensed">
        <thead>
        <tr style="border: 2px solid black;background: #8ed3d8;color: #000;">
            <td></td>
            <td style="border: 2px solid black;text-align: center">Budget</td>
            <td colspan="3" style="border: 2px solid black;text-align: center">Previous</td>
            <td colspan="3" style="border: 2px solid black;text-align: center">To-Date</td>
            <td colspan="1" style="border: 2px solid black; text-align: center">Remaining</td>
            <td colspan="2" style="text-align: center">At Completion</td>
        </tr>
        <tr style="background: #C6F1E7">
            <th class="col-xs-2" style="border: 2px solid black;text-align: center">Resource Type</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Base Line</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous (EV) Allowable</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Variance</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">to-date Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Allowable (EV) Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Todate Cost Variance</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Remaining Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">at Completion Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">at Completion Cost Variance</th>

        </tr>

        </thead>
        <tbody>
        @foreach($data as $key=>$value)
            <tr>
                <td style="border: 2px solid black;text-align: left">{{$value['name']}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['budget_cost']??0,2) }}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['previous_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['previous_allowable']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['previous_variance']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['to_date_cost'])}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['allowable_ev_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if($value['allowable_var']<0) color: red; @endif">{{number_format($value['allowable_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['remaining_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($value['completion_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if($value['cost_var']<0) color: red; @endif">{{number_format($value['cost_var']??0,2)}}</td>
            </tr>
        @endforeach
        <tr style="background: #F0FFF3">
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Total</th>
            <td style="border: 2px solid black;text-align: center; ">{{number_format($total['budget_cost'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['previous_cost'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['previous_allowable'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['previous_variance'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['to_date_cost'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['allowable_ev_cost'])}}</td>
            <td style="border: 2px solid black;text-align: center;@if($total['allowable_var']<0) color: red; @endif">{{number_format($total['allowable_var'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['remaining_cost'])}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($total['completion_cost'])}}</td>
            <td style="border: 2px solid black;text-align: center; @if($total['cost_var']<0) color: red; @endif">{{number_format($total['cost_var'])}}</td>

        </tr>
        </tbody>
    </table>
    <div class="row">
        <div id="chart" ></div>
        <div id="chart2" ></div>
    </div>
@endsection

@section('javascript')
    <script>
        var chart_data = {!! json_encode($at_comp_cost_var_chart)  !!};
        var second_chart = {!! json_encode($to_date_cost_var_chart)  !!};
        var data = {};
        var sites = [];
        var chart2 = {};
        var types = [];

        $.each(chart_data, function (e, value) {
            sites.push(e);
            data[e] = value.at_comp_cost_var
        })
        $.each(second_chart, function (e, value) {
            types.push(e);
            chart2[e] = value.to_date_cost_var
        })

        var chart = c3.generate({
            bindto: '#chart',
            data: {
                json: [data],
                keys: {value: sites},
                type: 'bar',

            },
            bar: {
                width:{ratio:1}
            },
            interaction: {
                enabled: true
            },
            axis: {
                y: {
                    label: {
                        text: 'At Completion Variance',
                        position: 'outer-middle',

                    }
                },
                x: {
                    label: {
                        text: 'Resource Type',
                        position: 'inner-top',
                    },
                    tick: {
                        centered: true
                    }

                }

            }

        });

        var chart = c3.generate({
            bindto: '#chart2',
            data: {
                json: [chart2],
                keys: {value: types},
                type: 'bar',
            },
            bar: {
                width:{ratio:1}
            },
            interaction: {
                enabled: true
            },
            axis: {
                y: {
                    label: {
                        text: 'To Date Cost Var',
                        position: 'outer-middle',
                    }
                },
                x: {
                    label: {
                        text: 'Resource Type',
                        position: 'inner-top',
                    }
                }

            }

        });


    </script>
@endsection