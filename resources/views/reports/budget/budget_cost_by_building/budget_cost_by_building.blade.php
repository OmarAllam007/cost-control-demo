@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">Budget Cost By Building</h2>
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
        <div class="tree--item">
            <div class="tree--item--label blue-first-level">
                <h5 style="font-size:20pt;font-family: 'Lucida Grande'"><strong>Total Project Budget Cost : {{number_format($total_budget,2)}} </strong></h5>
            </div>
        </div>

    </li>
    <br>
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.budget_cost_by_building._recursive_budget_by_building', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>


    <div id="chart-div" style="width:800px; margin:0 auto;"></div>
    <hr>
    <?=  \Lava::render('PieChart', 'BOQ', 'chart-div') ?>

    <div id="chart-div2" style="width:800px; margin:0 auto;"></div>
    <?= \Lava::render('ColumnChart', 'BudgetCost', 'chart-div2') ?>
@endsection
@section('javascript')

@endsection