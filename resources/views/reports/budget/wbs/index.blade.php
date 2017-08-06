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
        
    @endphp
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
            No WBS found
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
        background: hsl(0, 0%, 97%);
    }

    .level-1 td {
        background: hsl(0, 0%, 93%);
    }

    .level-2 td {
        background: hsl(0, 0%, 90%);
    }

    .level-3 td {
        background: hsl(0, 0%, 87%);
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