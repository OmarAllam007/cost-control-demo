@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Budget Cost v.s Dry Cost By Building</h2>
    <div class="pull-right">
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')
    <li class="list-unstyled" style="text-align:center;box-shadow: 5px 5px 5px #888888;
">
        <div class="panel panel-success">
            <table class="table col-md-6">
                <thead>
                <tr class="blue-first-level">
                    <td class="col-md-2">Total Dry Cost</td>
                    <td class="col-md-2">Total Budget Cost</td>
                    <td class="col-md-2">Total Difference</td>
                    <td class="col-md-2">Total Increase</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="col-md-2">{{number_format($total_dry,2)}}</td>
                    <td class="col-md-2">{{number_format($total_budget,2)}}</td>
                    <td class="col-md-2" @if($total_difference<0) style="color: #dd1144;" @endif>{{number_format($total_difference,2)}}</td>
                    <td class="col-md-2">{{number_format($total_increase)}} %</td>
                </tr>
                </tbody>
            </table>
        </div>
    </li>
    <br>
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.budget_cost_dry_building._recursive_budget_cost_dry_building', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
    <div id="chart-div" style="display: inline-flex;width: 100%"></div>
    <?=  \Lava::render('ColumnChart', 'BudgetCost', 'chart-div') ?>
    <br>
    <br>
    <br>
    <div id="chart-div2"></div>
    <?=  \Lava::render('ColumnChart', 'Difference', 'chart-div2') ?>
    <br>
    <br>
    <br>
    <div id="chart-div3"></div>
    <?=  \Lava::render('ColumnChart', 'Increase', 'chart-div3') ?>

@endsection
@section('javascript')

@endsection
