@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif
@section('header')
    <h2>{{$project->name}} - High Priority Materials Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=high-priority" target="_blank" class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
    <style>
        .checkList {
            width: 28px;
            height: 28px;
            position: relative;
            margin: 20px auto;
            background: #fcfff4;
            background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
            box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0, 0, 0, 0.5);

        label {
            width: 20px;
            height: 20px;
            position: absolute;
            top: 4px;
            left: 4px;
            cursor: pointer;
            background: linear-gradient(top, #222 0%, #45484d 100%);
            box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.5), 0px 1px 0px rgba(255, 255, 255, 1);

        &
        :after {
            content: '';
            width: 16px;
            height: 16px;
            position: absolute;
            top: 2px;
            left: 2px;
            background: $ activeColor;
            background: linear-gradient(top, $ activeColor 0%, $ darkenColor 100%);
            box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0, 0, 0, 0.5);
            opacity: 0;
        }

        &
        :hover::after {
            opacity: 0.3;
        }

        }
        input[type=checkbox] {
            visibility: hidden;

        &
        :checked + label:after {
            opacity: 1;
        }

        }
        }
    </style>
@endsection

@section('body')
    {{ Form::open(['route' => ["generate_top_matrial_reports.report",$project]])}}
    {{method_field('POST')}}{{csrf_field()}}
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.high_priority_materials._recursive_high_material_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
    <input type="submit" value="Next >>" class="btn btn-success">
    {{Form::close()}}
@endsection
@section('javascript')
@endsection
