@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._resource_dictionary')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Resource Dictionary Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=std-activity" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
        Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')

    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.resource_dictionary._recursive_resource_dictionary', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection
@section('javascript')
    <script>
        $('li').each(function(){ if (!$(this).find('tbody tr').length) { $(this).hide(); } })
    </script>
@endsection