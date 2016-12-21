@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_cost_by_break_down')
@endif
@section('header')
    <h2>Project Information</h2>
    <div class="pull-right">
        <a href="?print=1&paint=cost-break-down" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
<div class="blue-third-level">
   <h1>PROJECT DATES : </h1>
</div><br><br>
<table class="table table-bordered">
    <thead>
    <th>Project Cost Account No.</th>
    <th>Project Name</th>
    <th>Original Start date</th>
    <th>Original Finish Date</th>
    <th>Expected Finish Date</th>
    <th>Time elappsed</th>
    <th>Time remaining</th>
    </thead>
    <tbody>
    <tr></tr>
    </tbody>
</table>
@endsection