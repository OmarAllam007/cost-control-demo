@extends('layouts.app')

@section('title', 'Resources Rollup')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            {{$project->name}} &mdash; Semi Activity Rollup
        </h2>

        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back to project
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post" id="RollupForm">
        {{csrf_field()}}

        <wbs-tree :initial="{{$wbsTree}}" inline-template>
            <ul class="wbs-tree list-unstyled" id="wbs-tree">
                <wbs-level :initial="level" v-for="level in levels" depth="0"></wbs-level>
            </ul>
        </wbs-tree>

        <div class="form-group">
            <button class="btn btn-primary">Next <i class="fa fa-chevron-right"></i></button>
        </div>
    </form>
@endsection

@section('javascript')
    <script>
        window.units = {!! \App\Unit::select('id', 'type')->orderBy('type')->get() !!};
    </script>

    <script src="/js/rollup/semi-activity.js"></script>
@endsection