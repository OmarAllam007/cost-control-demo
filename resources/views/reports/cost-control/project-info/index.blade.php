@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif
@section('header')
    <h2>{{$project->name}} - Project Information Report</h2>

    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2">

            <section id="cost-summary">
                @include('reports.partials.cost-summary', $costSummary)
            </section>

            <div class="chart"
                 id="cpiChart"
                 data-type="bar"
                 data-labels="[&quot;Test 1&quot;, &quot;Test 2&quot;, &quot;Test 3&quot;]"
                 data-datasets="[{&quot;label&quot;: &quot;Data 1&quot;, &quot;data&quot;: [1, 4, 9]}, {&quot;label&quot;: &quot;Data 2&quot;, &quot;data&quot;: [1, 8, 27]}]"
            ></div>

        </div>
    </div>
@endsection

@section('javascript')
    <script src="/js/cost-info-charts.js"></script>
@append