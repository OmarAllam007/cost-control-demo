@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._budget_dry_building')
@endif
@section('header')
    <h2>Cost Performane By Significant Material</h2>
    <div class="pull-right">
        {{--<a href="?print=1&paint=cost-dry-building" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>--}}
        <a href="{{URL::previous()}}#report" class="btn btn-default btn-sm pull-right"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection
@section('body')

    <table class="table table-condensed">
        <thead class="output-cell">

        <tr>

            <td>NO.</td>
            <td>Resource Name</td>
            <td>Base Line</td>
            <td>Previous Cost</td>
            <td>Previous Allowable</td>
            <td>Previous Variance</td>
            <td>To Date Cost</td>
            <td>Allowable (EV) Cost</td>
            <td>To Date Variance</td>
            <td>Remaining Cost</td>
            <td>At Completion Cost</td>
            <td>Cost Variance</td>
        </tr>
        </thead>
        <tbody>
        <?php $i=1;?>
        @foreach($data as $key=>$value)
            <tr>
                <td><?php echo $i++?></td>
                <td class="col-md-1" >{{$key}}</td>
                <td>{{number_format($value['budget_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['previous_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['previous_allowable'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['previous_variance'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['to_date_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['allowable_ev_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['to_date_variance'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['remaining_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['at_completion_cost'] ?? 0 ,2)}}</td>
                <td>{{number_format($value['cost_variance'] ?? 0 ,2)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection