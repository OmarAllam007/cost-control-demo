@extends('layouts.app')

@section('header')
    <h3>{{$project->name}} &mdash; {{$revision->name}}</h3>

    <div class="pull-right">
        @can('budget_owner', $revision->project)
        <a href="{{$revision->url()}}/edit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
        @endcan
        <a href="{{$revision->url()}}/export" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Export Breakdown</a>
        <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
        <table class="table table-condensed table-bordered table-striped">
            <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">{{$rev1->name}}</th>
                <th class="text-right">{{$revision->name}}</th>
                <th class="text-right">Difference</th>
                <th class="text-right">Difference (%)</th>
            </tr>

            <tr class="{{$diffTotal > 0? 'bg-danger' : ($diffTotal < 0? 'bg-success' : '')}}">
                <th class="text-right">Total</th>
                <th class="text-right">{{number_format($firstRevisionTotal, 2)}}</th>
                <th class="text-right">{{number_format($thisRevisionTotal, 2)}}</th>
                <th class="text-right">{{number_format($diffTotal, 2)}}</th>
                <th class="text-right">{{number_format($diffPercentTotal, 2)}}%</th>
            </tr>
            </thead>

            @if ($disciplines->count())
            <tbody>
            @foreach($disciplines as $discipline)
                @php
                    $diff = ($thisRevision[$discipline]['cost'] ?? 0) - ($firstRevision[$discipline]['cost'] ?? 0);
                    if ($diff == 0) {
                        $diffPercent = 0;
                    } elseif (!empty($firstRevision[$discipline]['cost'])) {
                        $diffPercent = ($diff / $firstRevision[$discipline]['cost']) * 100;
                    } else {
                        $diffPercent = 100;
                    }
                @endphp
                <tr class="{{$diff > 0? 'bg-danger' : ($diff < 0? 'bg-success' : '')}}">
                    <td>{{$discipline}}</td>
                    <td class="text-right">{{number_format($firstRevision[$discipline]['cost'] ?? 0, 2)}}</td>
                    <td class="text-right">{{number_format($thisRevision[$discipline]['cost'] ?? 0, 2)}}</td>
                    <td class="text-right">{{number_format($diff, 2)}}</td>
                    <td class="text-right">{{number_format($diffPercent, 2)}}%</td>
                </tr>
            @endforeach
            </tbody>
            @endif
        </table>

        <div class="col-sm-8 col-sm-offset-2">
            <div id="chart" style="min-height: 400px; margin-top: 30px;"></div>
        </div>


@endsection

@section('javascript')
    @if ($disciplines->count())
    @php
        $columns = [$rev1->name => [$rev1->name], $revision->name => [$revision->name]];
        foreach ($disciplines as $discipline) {
            $columns[$rev1->name][] = ($firstRevision[$discipline]['cost'] ?? 0);
            if ($rev1->name != $revision->name) {
               $columns[$revision->name][] = ($thisRevision[$discipline]['cost'] ?? 0);
            }
        }
        $chartColumns = collect(array_values($columns));
    @endphp
    <script src="/js/d3.min.js"></script>
    <script src="/js/c3.min.js"></script>
    <script>
        c3.generate({
            bindto: '#chart',
            data: {
                columns: {!! $chartColumns !!},
                type: 'bar'
            },
            axis: {
                x: {
                    type: 'category',
                    categories: {!! $disciplines !!}
                }
            },
            grid: {
                x: {show: true},
                y: {show: true},
            }
        });
    </script>
    @endif
@endsection

@section('css')
    @if($disciplines->count()) <link rel="stylesheet" href="/css/c3.min.css"> @endif
@endsection