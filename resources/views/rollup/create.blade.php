@extends('layouts.app')

@section('title', 'Resources Rollup')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            {{$project->name}} &mdash; Resource Rollup
        </h2>
    </div>
@endsection

@section('body')
    <form action="" method="post" id="CreateRollupForm">
        <wbs-tree :initial="{{$wbsTree}}" inline-template>
            <ul class="wbs-tree list-unstyled" id="wbs-tree">
                <wbs-level :initial="level" v-for="level in levels" depth="0"></wbs-level>
            </ul>
        </wbs-tree>

    </form>
@endsection

@section('javascript')
    <script src="/js/rollup/create.js"></script>
@endsection