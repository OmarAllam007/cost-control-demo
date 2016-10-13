@extends('layouts.' . (request('print')? 'print' : 'app'))

@section('header')
    <h1>WBS-LEVELS</h1>
    <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i class="fa fa-chevron-left"></i>
        Back</a>
@endsection
@section('body')
    @if ($project->wbs_tree->count())
        <table class="table">
            <thead>
            <tr>
                <th width="25%">WBS Level 1</th>
                <th width="25%">WBS Level 2</th>
                <th width="25%">WBS Level 3</th>
                <th width="25%">WBS Level 4</th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->wbs_tree as $wbs_level)
                @include('wbs-level._recursive_report', ['wbs_level' => $wbs_level, 'tree_level' => 0])
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No WBS found</div>
    @endif
@endsection