@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Budget Cost By Building</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.js"></script>
@endsection
@section('body')
    <li class="list-unstyled" style="text-align:center;box-shadow: 5px 5px 5px #888888;
">
        <div class="tree--item">
            <div class="tree--item--label blue-first-level">
                <h5 style="font-size:20pt;font-family: 'Lucida Grande'"><strong>Total Project Budget Cost
                        : {{number_format($total_budget,2)}} </strong></h5>
            </div>
        </div>

    </li>

    <br>
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.budget_cost_by_building._recursive_budget_by_building', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>

    <div id="chart"></div><br><br>
    <div id="chart2" style="margin-left:200px"></div>

    <div id="chart-div" style="width:800px; margin:0 auto;"></div>

@endsection
@section('javascript')
    <script>

        var chart_data = {!! json_encode($data)  !!};

        var data = {};
        var sites = [];
        $.each(chart_data, function (e, value) {
            sites.push(value.name);
            data[value.name] = value.budget_cost;
        });

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
        console.log(data);


    </script>

@endsection