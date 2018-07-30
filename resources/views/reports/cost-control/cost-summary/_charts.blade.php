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