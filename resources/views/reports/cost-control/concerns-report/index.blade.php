@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('title', 'Issues &amp; Concerns | ' . $project->name)

@section('header')
    <h2 id="report_name">{{$project->name}} &mdash; Issues &amp; Concerns</h2>

    <div class="btn-toolbar pull-right">
        <a href="?excel" class="btn btn-success btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Excel</a>
        {{--<a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>--}}
        <a href="{{route('project.cost-control', $project)}}#report" class="btn btn-default btn-sm"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('body')

    <div class="row mb-3">
        <form action="" class="col-sm-6 col-md-4 display-flex" method="get">
            {{Form::select('period', $periods, $period->id,  ['placeholder' => 'Choose a Period', 'class'=>'form-control flex mr-10'])}}
            {{Form::submit('Submit', ['class'=>'btn btn-success'])}}
        </form>
    </div>

    @foreach($concerns as $name => $group)
        <h4 class="page-header">{{$name}}</h4>

        @foreach($group as $concern)
            <article class="panel panel-default">
                <div class="panel-body">
                    {!! nl2br(e($concern->comment)) !!}
                </div>

                @php
                    $data = json_decode($concern->data, true);
                @endphp

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="bg-primary">
                        @foreach($data as $key => $value)
                            <th>{{$key}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($data as $value)
                            <td>{{$value}}</td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </article>
        @endforeach
    @endforeach


@endsection

@section('javascript')

@endsection

@section('css')

@endsection