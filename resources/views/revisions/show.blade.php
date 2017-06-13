@extends('layouts.app')

@section('header')
    <h3>{{$project->name}} &mdash; {{$revision->name}}</h3>

    <div class="pull-right">
        <a href="{{$revision->url()}}/export" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Export Datasheet</a>
        <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Description</th>
            <th>{{$rev1->name}}</th>
            <th>{{$revision->name}}</th>
            <th>Difference</th>
            <th>Difference (%)</th>
        </tr>
        </thead>
        <tbody>
            @foreach($disciplines as $discipline)
                @php
                $diff = $thisRevision[$discipline]['cost'] - $firstRevision[$discipline]['cost'];
                if ($diff == 0) {
                    $diffPercent = 0;
                } elseif ($firstRevision[$discipline]['cost']) {
                    $diffPercent = ($diff / $firstRevision[$discipline]['cost']) * 100;
                } else {
                    $diffPercent = 100;
                }
                @endphp
                <tr class="{{$diff > 0? 'bg-danger' : ($diff < 0? 'bg-success' : '')}}">
                    <td>{{$discipline}}</td>
                    <td>{{number_format($firstRevision[$discipline]['cost'], 2)}}</td>
                    <td>{{number_format($thisRevision[$discipline]['cost'], 2)}}</td>
                    <td>{{number_format($diff, 2)}}</td>
                    <td>{{number_format($diffPercent, 2)}}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
            $firstRevisionTotal = $firstRevision->sum('cost');
            $thisRevisionTotal = $thisRevision->sum('cost');
            $diffTotal = $thisRevisionTotal - $firstRevisionTotal;
            $diffPercentTotal = ($diffTotal/$firstRevisionTotal) * 100;
            @endphp
            <tr class="{{$diffTotal > 0? 'bg-danger' : ($diffTotal < 0? 'bg-success' : '')}}">
                <th>Total</th>
                <th>{{number_format($firstRevisionTotal, 2)}}</th>
                <th>{{number_format($thisRevisionTotal, 2)}}</th>
                <th>{{number_format($diffTotal, 2)}}</th>
                <th>{{number_format($diffPercentTotal, 2)}}%</th>
            </tr>
        </tfoot>
    </table>

    <div class="col-sm-8 col-sm-offset-2">
        <div id="chart" style="min-height: 400px; margin-top: 30px;"></div>
    </div>

@endsection

@section('javascript')
    @php
        $columns = [$rev1->name => [$rev1->name], $revision->name => [$revision->name]];
        foreach ($disciplines as $discipline) {
            $columns[$rev1->name][] = $firstRevision[$discipline]['cost'];
            $columns[$revision->name][] = $thisRevision[$discipline]['cost'];
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
@endsection

@section('css')
    <link rel="stylesheet" href="/css/c3.min.css">
@endsection