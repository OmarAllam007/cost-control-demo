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
    <form action="#" method="post" id="RollupForm">
        {{csrf_field()}}

        <div class="search-form row">
            <div class="form-group col-sm-3">
                <button type="button" @click="show_wbs_modal" class="btn btn-default btn-sm btn-block">
                    <i class="fa fa-building-o"></i>
                    @{{ wbs.code? wbs.code : 'Select WBS' }}
                </button>
            </div>

            <activity-list :wbs="wbs"></activity-list>
        </div>

        <wbs-modal :selected="wbs" :levels="{{$wbsTree}}"></wbs-modal>

        <rollup-form :wbs="wbs" :code="activity_code"></rollup-form>
    </form>
@endsection

@section('javascript')
    <script>
        window.units = {!! \App\Unit::select('id', 'type')->orderBy('type')->get() !!};
    </script>

    <script src="/js/rollup/semi-activity.js"></script>
@endsection