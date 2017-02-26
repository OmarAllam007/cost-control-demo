@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_discipline')
@endif
@section('header')
    <h2>Budget Cost By Discipline</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-discipline" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.js"></script>
@endsection

@section('body')

    <table class="table table-condensed">
        <thead class="output-cell">
        <tr>

            <th class="col-xs-4">Discipline</th>
            <th class="col-xs-4">Budget Cost</th>
            <th class="col-xs-4">Weight</th>
        </tr>
        </thead>
        <tbody>
        @foreach($survey as $key=>$row)
            <tr class="tbl-content">

                <td class="col-xs-4">{{$key}}</td>
                <td class="col-xs-4">{{number_format($row['budget_cost'],2)}}</td>
                <td class="col-xs-4">%{{number_format($row['weight'])}}</td>
            </tr>
        @endforeach
        <tr style="border-top: solid #000000">

            <td class="col-xs-4 output-cell" style="font-weight: 800">Grand Total</td>
            <td class="col-xs-4 output-cell">{{number_format($total,2)}}</td>
            <td class="col-xs-4 output-cell">%{{number_format($totalWeight,2)}}</td>

            {{--<td class="col-xs-2">% {{ceil($total['total_increase'])}}</td>--}}
        </tr>
        </tbody>
    </table>
    <div id="chart" style="width:800px; margin:0 auto;"></div><br>
    <div id="chart2" style="width:800px; margin:0 auto;"></div>
    <hr>


@endsection

@section('javascript')
    <script>
        var chart_data = {!! json_encode($survey)  !!};

        var data = {};
        var sites = [];
        $.each(chart_data, function (e, value) {
            sites.push(e);

            data[e] = value.budget_cost
        })

        var chart = c3.generate({
            bindto: '#chart',
            data: {
                json: [data],
                keys: {value: sites},
                type: 'bar',
                labels: true,
            },
            bar: {
                // or
                width: {
                    ratio: .9 // this makes bar width 50% of length between ticks
                }
            },
            interaction: {
                enabled: true
            },
            axis: {
                y: {
                    label: {
                        text: 'Budget Cost',
                    }
                },
                x: {
                    label: {
                        text: 'WBS-LEVEL',
                    }
                }

            }

        });


        var chart = c3.generate({
            bindto: '#chart2',

            data: {
                json: [data],
                keys: {value: sites},
                type: 'pie',
                labels: true,
            },
            size: {
                width: 800,
                height: 500,
            },
            interaction: {
                enabled: true
            }

        });
    </script>
@endsection