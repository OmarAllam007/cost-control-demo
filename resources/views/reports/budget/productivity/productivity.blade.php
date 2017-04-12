@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._productivity')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Productivity Report</h2>
    <div class="pull-right">
        <a href="{{route('export.budget_productivity',
        ['project'=>$project])}}"
           class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i>
            Export</a>
        <a href="?print=1" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
    <style>
        .fixed {
            position: fixed;
            top: 0;
            height: 70px;
            z-index: 1;
        }
    </style>
@endsection
@section('body')
    <div class="row" style="margin-bottom: 10px;">
        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#CSICategoryModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select
                Category</a>
            <a href="#" class="remove-tree-input btn btn-warning" data-target="#ActivitiesModal"
               data-label="Select Activity"><span class="fa fa-times-circle"></span></a>

        </div>
    </div>

    <ul class="list-unstyled tree report_tree">
        @foreach($tree as $parentKey=>$category)
            @include('reports.budget.productivity._recursive_productivity', ['category'=>$category,'tree_level'=>0])
        @endforeach
    </ul>
    <input type="hidden" value="{{$project->id}}" id="project_id">

    @include('productivity._category_modal')
@endsection

@section('javascript')
    <script>
        $(function () {
            var global_selector = '';
            var project_id = $('#project_id').val();
            var productivity = 0;
            $('.tree-radio').on('change', function () {
                var value = $(this).attr('value');
                global_selector = $('#' + value);
                $('.division-container').removeClass('in').addClass('hidden');
                $('.target').removeClass('in').addClass('hidden');
                global_selector.parents('.division-container').addClass('in').removeClass('hidden');
                global_selector.addClass('in target').removeClass('hidden');
                global_selector.parents().each(function () {
                    $(this).addClass('target').removeClass('hidden')
                });
                $('ul.report_tree > li:not(.target)').addClass('hidden');
                productivity = value;
            });

            $('.remove-tree-input').on('click', function () {
                global_selector.parents('.division-container').removeClass('in').removeClass('hidden');
                global_selector.removeClass('in').addClass('hidden');
                global_selector.parents('.division-container').removeClass('target').addClass('hidden');
                global_selector.removeClass('target');
                $('li').not('target').removeClass('hidden');
                $('.division-container').removeClass('in').removeClass('hidden');
                $('li.target').removeClass('target');
                $('ul.report_tree > li:not(.target)').removeClass('hidden');
            });

            $('.print').on('click', function () {
                sessionStorage.removeItem('budget_productivity_' + project_id);
                sessionStorage.setItem('budget_productivity_' + project_id, productivity);
            });
        })

    </script>
@endsection