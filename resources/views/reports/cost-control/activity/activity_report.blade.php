@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._standard-activity')
@endif
@section('header')
    <h2 class="">Activity report</h2>
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
        @foreach($tree as $level)
            @include('reports.cost-control.activity._recursive_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>


    </script>
@endsection