@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Activity report</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>--}}
        {{--Print</a>--}}
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection
@section('body')
    <style>
        .padding{
            padding-right: 300px;
        }
    </style>
    <div class="col-md-12 panel panel-default boqLevelFour">
        <div class="col-md-12 boqLevelFour">
            <table class="col-md-12">
                <thead>
                <tr style="text-align: center">
                    <td>Base Line</td>
                    <td>Previous Cost</td>
                    <td>Previous Allowable</td>
                    <td>Previous Var</td>
                    <td>To Date Cost</td>
                    <td>Allowable (EV) Cost</td>
                    <td>Remaining Cost</td>
                    <td>To Date Variance</td>
                    <td>At Completion Cost</td>
                    <td>Cost Variance</td>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align: center">
                    <td>{{number_format($total['budget_cost']??0,2) }}</td>
                    <td>{{number_format($total['prev_cost']??0,2)}}</td>
                    <td>{{number_format($total['prev_allowable']??0,2)}}</td>
                    <td>{{number_format($total['prev_variance']??0,2)}}</td>
                    <td>{{number_format($total['to_data_cost']?? 0,2)}}</td>
                    <td>{{number_format($total['to_date_allowable_cost']??0,2)}}</td>
                    <td>{{number_format($total['allowable_var']??0,2)}}</td>
                    <td>{{number_format($total['remain_cost']??0,2)}}</td>
                    <td>{{number_format($total['completion_cost']??0,2)}}</td>
                    <td style=" @if($total['cost_var'] <0)  color: red; @endif ">{{number_format($total['cost_var']??0,2)}}</td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>
    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost.activity_report',$project)}}" class="form-inline col col-md-8" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Session::has('period_id'.$project->id) ? Session::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>
    </div>
    <ul class="list-unstyled tree">
        @foreach($tree as $level)
            @include('reports.cost-control.activity._recursive_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>


    </script>
@endsection