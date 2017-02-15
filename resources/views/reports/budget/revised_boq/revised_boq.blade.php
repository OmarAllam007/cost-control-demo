@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._revised_boq')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Revised BOQ Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=revised_boq" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')

    <br>
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.revised_boq._recursive_revised_boq', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection


@section('javascript')
    <script>


    </script>
@endsection