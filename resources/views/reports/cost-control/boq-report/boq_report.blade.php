@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - BOQ Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
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
    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost.boq_report',$project)}}" class="form-inline col col-md-8" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Session::has('period_id'.$project->id) ? Session::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>
    </div>

    <ul class="list-unstyled tree">
        @foreach($tree as $key=>$wbs_level)
            @include('reports.cost-control.boq-report._recursive_report', ['level'=>$wbs_level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>
        $('li').each(function(){ if (!$(this).find('tbody tr').length) { $(this).hide(); } })
    </script>
@endsection