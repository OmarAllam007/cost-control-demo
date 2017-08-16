@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._wbs')
@endif
@section('header')
    <div class="display-flex">
    <h2 class="flex">WBS Levels &mdash; {{$project->name}}</h2>
    <div>
        <a href="?excel" class="btn btn-info btn-sm"><i class="fa fa-cloud-download"></i> Export</a>
        <a href="?print=1&paint=wbs" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{route('project.show', $project)}}#Reports" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>
    </div>
@endsection

@section('body')
    @if ($wbsTree->count())
        <table class="table table-condensed table-bordered" id="report-table">
            <thead>
               <tr class="bg-primary">
                   <th>Wbs Level</th>
                   <th>Budget Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wbsTree as $wbs_level)
                    @include('reports.budget.wbs._recursive', ['wbs_level' => $wbs_level, 'tree_level' => 0])
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i>
            No WBS levels found
        </div>
    @endif
@endsection

@section('javascript')
<script>
    $('.open-level').click(function(e) {
        e.preventDefault();
        const target = $('.' + $(this).data('target'));

        if (target.hasClass('hidden')) {    
            target.removeClass('hidden');
            $(this).find('i.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
        } else {
            closeRecursive(this);
        }
    });

    $('#report-table tbody > tr').click(function(e) {
        const isHighlighted = $(this).hasClass('highlighted');

        $('#report-table tbody tr').removeClass('highlighted');
        if (!isHighlighted) {
            $(this).addClass('highlighted');
        }
    });

    function closeRecursive(elem) { 
        const target = $('.' + $(elem).data('target'));       
        target.addClass('hidden').each(function(){
            closeRecursive($(this).find('a'));
        });

        $(elem).find('i.fa').removeClass('fa-minus-square').addClass('fa-plus-square');
    }
</script>
@endsection

@section('css')
<style>

    #report-table tbody tr:hover > td {
        background-color: rgba(255, 255, 204, 0.7);
    }

    #report-table tbody tr.highlighted > td {
        background-color: #ffc;
    }

    .level-0 td {
        background: #f7f7f7;
    }

    .level-1 td {
        background: #ededed;
    }

    .level-2 td {
        background: #e6e6e6;
    }

    .level-3 td {
        background: #dedede;
    }

    .level-1 .level-label {
        padding-left: 20px;
    }

    .level-2 .level-label {
        padding-left: 40px;
    }

    .level-3 .level-label {
        padding-left: 60px;
    }

    .level-4 .level-label {
        padding-left: 80px;
    }
</style>
@endsection