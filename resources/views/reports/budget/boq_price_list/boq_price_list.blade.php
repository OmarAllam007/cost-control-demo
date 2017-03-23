@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._qs_summery')
@endif
@section('header')
    <h2 class="">{{$project->name}} - BOQ PRICE LIST Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=boq-price" target="_blank" class="btn btn-default btn-sm print"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm back">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')
    <div class="row" style="margin-bottom: 10px;">
        <div class="btn-group btn-group-sm  btn-group-block col-md-2">
            <a href="#WBSModal" data-toggle="modal" class="btn btn-default btn-block  tree-open">Select WBS-Level</a>
            <a href="#" class="remove-tree-input-wbs btn btn-warning" data-target="#WBSModal"
               data-label="Select WBS-Level"><span class="fa fa-times-circle"></span></a>

        </div>
    </div>
    <ul class="list-unstyled tree report_tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.boq_price_list._recursive_report', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
    @include('wbs-level._modal')
    <input type="hidden" value="{{$project->id}}" id="project_id">
@endsection
@section('javascript')
    <script>
        var project_id = $('#project_id').val();
        var wbs = 0;
        var global_selector ='';
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
            }
        });

        $('.remove-tree-input-wbs').on('click', function () {
            console.log(global_selector)
            global_selector.parents('.level-container').removeClass('in').removeClass('hidden');
            global_selector.removeClass('in').addClass('hidden');
            global_selector.parents('li').removeClass('target').addClass('hidden');
            global_selector.removeClass('target');
            $('li').not('target').removeClass('hidden');
            $('.level-container').removeClass('in').removeClass('hidden');
            global_selector.children().children().children('article').removeClass('in').addClass('hidden');
            $(this).prev('a').text('Select WBS-Level');
            wbs=0;

        });

        $('.print').on('click',function () {
            console.log(wbs)
            sessionStorage.removeItem('wbs_'+project_id);
            sessionStorage.setItem('wbs_'+project_id,wbs);
        })
    </script>
@endsection