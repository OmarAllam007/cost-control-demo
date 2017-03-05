@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._wbs')
@endif
@section('header')
    <h1>{{$project->name}} - WBS-LEVELS</h1>
    <div class="pull-right">
        <a href="{{route('wbs_report.export',
        ['project'=>$project])}}"
           target="_blank" class="btn
        btn-info
        btn-sm"><i class="fa fa-cloud-download"></i>
            Export</a>
        <a href="?print=1&paint=wbs" target="_blank"
           class="btn btn-default btn-sm"><i
                    class="fa fa-print"></i>
            Print</a>

        <a href="{{route('project.show', $project)}}#report"
           class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
@endsection

@section('body')
    @php
        $depthTree = $project->tree_depth+1;
    @endphp
    @if ($project->wbs_tree->count())
        <table class="table table-condensed table-bordered">
            <thead>
            @for($depth=1;$depth< $depthTree;++$depth)
                <td class="blue-first-level" >WBS
                    Level {{$depth}}
                </td>
            @endfor
            </thead>
            <tbody>
            @foreach($wbsTree as $wbs_level)
                @include('wbs-level._recursive_report', ['wbs_level' => $wbs_level, 'tree_level' => 0,'child'=>false])
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning"><i
                    class="fa fa-exclamation-triangle"></i>
            No WBS found
        </div>
    @endif

@endsection