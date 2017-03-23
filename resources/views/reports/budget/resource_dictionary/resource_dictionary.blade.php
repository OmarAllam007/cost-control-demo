@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._resource_dictionary')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Resource Dictionary Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=resource-dictionary" target="_blank" class="btn btn-default btn-sm print"><i
                    class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')
    <div class="row" style="margin-bottom: 10px;">
        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#ResourceTypeModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select
                Resource Type</a>
            <a href="#" class="remove-tree-input-type btn btn-warning" data-target="#ResourceTypeModal"
               data-label="Select Resource-Type"><span class="fa fa-times-circle"></span></a>
        </div>

    </div>
    <ul class="list-unstyled tree report_tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.resource_dictionary._recursive_resource_dictionary', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>

    <input type="hidden" value="{{$project->id}}" id="project_id">
    @include('resource-type._modal')
@endsection
@section('javascript')
    <script>
        var type = 0;
        var global_selector = '';
        var project_id = $('#project_id').val();
        $('.tree-radio').on('change', function () {
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
                type = value;

            }
        });
        $('.remove-tree-input-type').on('click', function () {
            global_selector.parents('.level-container').removeClass('in').removeClass('hidden');
            global_selector.removeClass('in').addClass('hidden');
            global_selector.parents('li').removeClass('target').addClass('hidden');
            global_selector.removeClass('target');
            $('li').not('target').removeClass('hidden');
            $('.level-container').removeClass('in').removeClass('hidden');
            global_selector.children().children().children('article').removeClass('in').addClass('hidden');
            $(this).prev('a').text('Select Resource Type');
            type = 0;
        });

        $('.print').on('click', function () {
            sessionStorage.removeItem('dictionary_' + project_id;
            sessionStorage.setItem('dictionary_' + project_id, type);
        });
    </script>

    <script src="{{asset('/js/project.js')}}"></script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection