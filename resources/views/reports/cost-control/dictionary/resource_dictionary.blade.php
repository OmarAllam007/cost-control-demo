@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Cost Performane By Resource Dictionary Report</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>--}}
        {{--Print</a>--}}
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    <ul class="list-unstyled tree">
        @foreach($tree as $resource_type)
            @include('reports.cost-control.dictionary._recursive_report', ['type'=>$resource_type,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>


    </script>
@endsection