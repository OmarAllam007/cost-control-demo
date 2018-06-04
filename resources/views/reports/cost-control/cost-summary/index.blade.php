@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('title', 'Cost Summary Report | ' . $project->name)

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css" rel="stylesheet"/>
@endsection

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

    <div class="row" style="margin-bottom: 10px;">
        <form action="" class="form-inline col-sm-6 col-md-4" method="get">
            {{Form::select('period', \App\Period::where('project_id',$project->id)->readyForReporting()->pluck('name','id'), Session::get('period_id_'.$project->id),  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>
    </div>

    <table class="table table-condensed cost-summary-table">
        <thead>
        <tr style="border: 2px solid black;background: #8ed3d8;color: #000;">
            <td></td>
            <th class="col-sm-2" rowspan="2" style="border: 2px solid black;text-align: center">Resource Type</th>
            <th style="border: 2px solid black;text-align: center">Budget</th>
            <th style="border: 2px solid black;text-align: center">Previous</th>
            <th colspan="3" style="border: 2px solid black;text-align: center">To-Date</th>
            <th colspan="1" style="border: 2px solid black; text-align: center">Remaining</th>
            <th colspan="3" style="text-align: center; border: 2px solid black;">At Completion</th>
        </tr>
        <tr style="background: #C6F1E7">
            <td></td>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Base Line</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">To Date Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Allowable (EV) Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">To Date Cost Variance</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Remaining Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">At Completion Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">At Completion Cost Variance</th>
            {{--<th class="col-xs-1" style="border: 2px solid black;text-align: center">Concern</th>--}}
        </tr>

        </thead>
        <tbody>
        @foreach($toDateData as $typeToDateData)
            <tr>
                <td>
                    <a  href="#" class="btn btn-primary btn-lg concern-btn" title="Add issue or concern"
                        data-json="{{ json_encode(['Base Line' => $typeToDateData['budget_cost'], 'Previous Cost' => $typePreviousData['previous_cost'], 'Todate Cost' => $typeToDateData['to_date_cost'], 'Allowable (EV) Cost' => $typeToDateData['ev'], 'Todate Cost Variance' => $typeToDateData['to_date_var'], 'Remaining Cost' => $typeToDateData['remaining_cost'], 'At Completion Cost' => $typeToDateData['completion_cost'], 'At Completion Cost Variance' => $typeToDateData['completion_cost_var']]) }}">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </a>
                </td>
                <td style="border: 2px solid black;text-align: left">{{$typeToDateData->type}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['budget_cost']??0,2) }}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['previous_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['to_date_cost']??0, 2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['ev']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if(($typeToDateData['to_date_var'] ?? 0) < 0) color: red; @endif">{{number_format($typeToDateData['to_date_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['remaining_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['completion_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if(($typeToDateData['completion_cost_var']??0)<0) color: red; @endif">{{number_format($typeToDateData['completion_cost_var']??0,2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background: #F0FFF3">
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Total</th>
            <td style="border: 2px solid black;text-align: center;">{{number_format($toDateData->sum('budget_cost'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('previous_cost'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('to_date_cost'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('ev'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center;@if($toDateData->sum('to_date_var') <0) color: red; @endif">{{number_format($toDateData->sum('to_date_var'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('remaining_cost'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('completion_cost'), 2)}}</td>
            <td style="border: 2px solid black;text-align: center; @if($toDateData->sum('completion_cost_var')<0) color: red; @endif">{{number_format($toDateData->sum('completion_cost_var'), 2)}}</td>

        </tr>
        </tfoot>
    </table>


    <input type="hidden" value="{{$project->id}}" id="project_id">

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

    @include('reports.partials.concerns-modal')
    {{--@if(count($concerns))--}}
    {{--@include('reports._cost_summery_concerns')--}}
    {{--@endif--}}
@endsection

@section('javascript')
   <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.js"></script>
    @php
        //todo: Extract all this into view composer or controller
        $to_date_values = collect(['To Date Cost']);
        $budget_values = collect(['Budget Cost']);
        $completion_values = collect(['At Completion']);
        $allowable_values = collect(['Allowable Cost']);
        $typeNames = collect();
        foreach ($toDateData as $type) {
            $to_date_values[] = $type->to_date_cost ?: 0;
            $completion_values[] = $type->completion_cost ?: 0;
            $allowable_values[] = $type->ev ?: 0;
            $budget_values[] = $type->budget_cost ?: 0;
            $typeNames[] = $type->type;
        }

        $costTrends = \App\MasterShadow::with('period')->orderBy('period_id')
            ->where('project_id', $project->id)->groupBy('period_id')
            ->selectRaw('period_id, sum(completion_cost) completion_cost, sum(cost_var) as cost_var')
            ->selectRaw('SUM(CASE WHEN activity_id = 3060 THEN budget_cost END) as reserve')
            ->get();

        $trends_completion_cost_values = collect(['At Completion Cost']);
        $trends_completion_cost_var_values = collect(['At Completion Cost Variance']);
        $periods = collect();
        foreach ($costTrends as $trend) {
            $periods[] = $trend->period->name;
            $trends_completion_cost_values[] = $trend->completion_cost - $trend->reserve;
            $trends_completion_cost_var_values[] = $trend->cost_var + $trend->reserve;
        }
    @endphp


    <script>

        var budget_cost_vs_completion_chart = c3.generate({
            bindto: '#budget_cost_vs_completion_chart',
            data: {
                columns: [{!! $budget_values->values() !!}, {!! $completion_values->values() !!}],
                type: 'bar'
            },
            bar: {
                width: {ratio: .5}
            },
            transition: {duration: 100},
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $typeNames !!}
                },
                y: {
                    tick: {
                        format: d3.format(",.2f")
                    }
                }
            },
            grid: {
                x: {show: true},
                y: {show: true}
            }
        });

        var to_date_vs_allowable_chart = c3.generate({
            bindto: '#to_date_vs_allowable_chart',
            data: {
                columns: [{!! $to_date_values->values() !!}, {!! $allowable_values->values() !!}],
                type: 'bar'
            },
            bar: {
                width: {ratio: .5}
            },
            transition: {
                duration: 100
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $typeNames !!}
                },
                y: {
                    tick: {
                        format: d3.format(",.2f")
                    }
                }
            },
            grid: {
                x: {show: true},
                y: {show: true}
            }
        });

        var completion_cost_trend_chart = c3.generate({
            bindto: '#completion_cost_trend_chart',
            data: {
                columns: [{!! $trends_completion_cost_values !!}],
                type: 'line'
            },
            transition: {
                duration: 100
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $periods !!}
                },
                y: {
                    tick: {
                        format: d3.format(",.2f")
                    }
                },
            },
            grid: {
                x: {show: true},
                y: {show: true}
            }
        });

        var completion_cost_var_trend_chart = c3.generate({
            bindto: '#completion_cost_var_trend_chart',
            data: {
                columns: [{!! $trends_completion_cost_var_values !!}],
                type: 'line'
            },
            transition: {
                duration: 100
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $periods !!}
                },
                y: {
                    tick: {
                        format: d3.format(",.2f")
                    }
                },
            },
            grid: {
                x: {show: true},
                y: {show: true}
            }
        });

        {{--
        var data = {};
        var sites = [];
        var chart2 = {};
        var types = [];

        $.each(chart_data, function (e, value) {
            sites.push(e);
            data[e] = value.at_comp_cost_var
        });
        $.each(second_chart, function (e, value) {
            types.push(e);
            chart2[e] = value.to_date_cost_var
        });

        var chart = c3.generate({
            bindto: '#chart',
            data: {
                json: [data],
                keys: {value: sites},
                type: 'bar',
            },
            bar: {
                width: {ratio: .25}
            },
            transition: {
                duration: 100
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
                }

            }

        });

        var chart3 = c3.generate({
            bindto: '#chart2',
            data: {
                json: [chart2],
                keys: {value: types},
                type: 'bar',
            },
            bar: {
                width: {ratio: .5}
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

        $(function () {
            var ConcernModal = $('#ConcernModal');
            var ConcernModalForm = ConcernModal.find('form');
            var title = ConcernModal.find('.modal-title');
            var project_id = $('#project_id').val();


            $('.concern-btn').on('click', function (e) {
                e.preventDefault();
                var data = ($(this).attr('data-json'));
                ConcernModal.data('json', data).modal();

            });

            $('.apply_concern').on('click', function (e) {
                e.preventDefault();
                var report_name = 'Cost Summary Report';
                var body = $('#mytextarea').val();
                var data = ConcernModal.data('json');
                if (body.length != 0) {
                    $.ajax({
                        url: '/concern/' + project_id,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            info: data,
                            report_name: report_name,
                            comment: body,
                        },
                    }).success((e) => {
                        console.log('success')
                    });
                    ConcernModal.modal('hide');
//                    location.reload();
                }
            })

        })
--}}
    </script>
@endsection