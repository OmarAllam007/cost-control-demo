@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Quantity Survey Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=survey" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i>
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
            <a href="#WBSModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select WBS-Level</a>
            <a href="#" class="remove-tree-input-wbs btn btn-warning" data-target="#WBSModal"
               data-label="Select WBS-Level"><span class="fa fa-times-circle"></span></a>

        </div>
        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#ActivitiesModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select
                Activity</a>
            <a href="#" class="remove-tree-input-activity btn btn-warning" data-target="#ActivitiesModal"
               data-label="Select Activity"><span class="fa fa-times-circle"></span></a>

        </div>
    </div>
    <br>
    <ul class="list-unstyled tree report_tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.qs_summery._recursive_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>

    @include('std-activity._modal', ['input' => 'activity', 'value' => 'Select Activity'])
    @include('wbs-level._modal')
    <input type="hidden" value="{{$project->id}}" id="project_id">

@endsection
@section('javascript')
    <script>
        var target_td = '';
        var project_id = $('#project_id').val();
        var activity = 0;
        var wbs = 0;
        var global_selector;
        $('.wbs-radio').on('change', function () {
            if (this.checked) {
                var value = $(this).attr('value');
                global_selector = $('#col-' + value);
                $('.level-container').removeClass('in').addClass('hidden');
                global_selector.parents('.level-container').addClass('in').removeClass('hidden');
                global_selector.addClass('in').removeClass('hidden');
                global_selector.parents('li').addClass('target').removeClass('hidden');
                global_selector.children().children().children('article').addClass('in').removeClass('hidden');
                global_selector.parents().each(function () {
                    $(this).addClass('target').removeClass('hidden')
                });
                $('ul.report_tree > li:not(.target)').addClass('hidden');

                wbs=value;
                activity=0;
            }
        });
        $('.remove-tree-input-wbs').on('click', function () {
            global_selector.parents('.level-container').removeClass('in').removeClass('hidden');
            global_selector.removeClass('in').addClass('hidden');
            global_selector.parents('li').removeClass('target').addClass('hidden');
            global_selector.removeClass('target');
            $('li').not('target').removeClass('hidden');
            $('.level-container').removeClass('in').removeClass('hidden');
            global_selector.children().children().children('article').removeClass('in').addClass('hidden');
            $(this).prev('a').text('Select WBS-Level');
            wbs=0;
            activity=0;

        });

        $('.activity-input').on('change', function () {
            var value = $(this).val();
            target_td = $("#activity-" + value);
            target_td.parents('.level-container').addClass('in').removeClass('hidden');
            target_td.addClass('in').removeClass('hidden');
            target_td.parents('li').addClass('target').removeClass('hidden');
            $('ul.report_tree > li:not(.target)').addClass('hidden');
            activity=value;
            wbs=0;

        });

        $('.remove-tree-input-activity').on('click', function () {
            target_td.parents('.level-container').removeClass('in').removeClass('hidden');
            target_td.removeClass('in').addClass('hidden');
            target_td.parents('li').removeClass('target').addClass('hidden');
            target_td.removeClass('target');
            $('li').not('target').removeClass('hidden');
            $('.level-container').removeClass('in').removeClass('hidden');
//                target_td.parent('tr').css('background-color', 'white');
//                target_td.children().children().children('article').removeClass('in').addClass('hidden');
            $(this).prev('a').text('Select Activity');
            wbs=0;
            activity=0;
        });

        $('.print').on('click',function () {
            sessionStorage.removeItem('activity_'+project_id);
            sessionStorage.removeItem('wbs_'+project_id);
            sessionStorage.setItem('activity_'+project_id,activity);
            sessionStorage.setItem('wbs_'+project_id,wbs);
        })

    </script>
    <script src="{{asset('/js/project.js')}}"></script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection