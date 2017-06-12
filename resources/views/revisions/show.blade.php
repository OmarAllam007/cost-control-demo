@extends('layouts.app')

@section('header')
    <h3>{{$project->name}} &mdash; {{$revision->name}}</h3>

    <div class="pull-right">
        <a href="{{$revision->url()}}/export" class="btn btn-sm btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
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
                $diff = $thisRevision[$discipline] - $firstRevision[$discipline];
                $diffPercent = ($diff / $firstRevision[$discipline]) * 100
                @endphp
                <tr class="{{$diff > 0? 'bg-danger' : ($diff < 0? 'bg-success' : '')}}">
                    <td>{{$discipline}}</td>
                    <td>{{number_format($firstRevision[$discipline], 2)}}</td>
                    <td>{{number_format($thisRevision[$discipline], 2)}}</td>
                    <td>{{number_format($diff, 2)}}</td>
                    <td>{{number_format($diffPercent, 2)}}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
            $firstRevisionTotal = $firstRevision->sum();
            $thisRevisionTotal = $thisRevision->sum();
            $diffTotal = $thisRevision->sum() - $firstRevision->sum();
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

@endsection