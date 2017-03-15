@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._revised_boq')
@endif
@section('header')
    <h2 class="">{{$project->name}} - Revised BOQ Report</h2>
    <div class="pull-right">
        <a href="?print=1&paint=revised_boq" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i>
            Print</a>
        <a href="{{route('project.show', $project)}}#report" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </div>

@endsection
@section('body')
    <div class="col-md-12 panel panel-default boqLevelOne" >
        <div class="col-md-12 boqLevelOne">
            <table class="col-md-12">
                <thead>
                <tr style="text-align: center">
                    <td class="col-md-6">Total Original BOQ</td>
                    <td class="col-md-6">Total Revised BOQ</td>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align: center">
                    <td class="col-md-6">{{number_format($total['original'])}}</td>
                    <td class="col-md-6">{{number_format($total['revised'])}}</td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>
    <ul class="list-unstyled tree">
        @foreach($tree as $parentKey=>$level)
            @include('reports.budget.revised_boq._recursive_revised_boq', ['level'=>$level,'tree_level'=>0])
        @endforeach
    </ul>
@endsection


@section('javascript')
    <script>


    </script>
@endsection