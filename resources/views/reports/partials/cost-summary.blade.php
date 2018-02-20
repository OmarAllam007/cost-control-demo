<h4 class="card-title section-header dark-cyan">Cost Summary</h4>
<section class="card-body">
    <table class="table table-condensed cost-summary-table">
        <thead>
        <tr style="border: 2px solid black;background: #8ed3d8;color: #000;">
            <th class="col-sm-2" rowspan="2" style="border: 2px solid black;text-align: center">Resource Type</th>
            <th style="border: 2px solid black;text-align: center">Budget</th>
            <th colspan="3" style="border: 2px solid black;text-align: center">Previous</th>
            <th colspan="3" style="border: 2px solid black;text-align: center">To-Date</th>
            <th colspan="1" style="border: 2px solid black; text-align: center">Remaining</th>
            <th colspan="3" style="text-align: center; border: 2px solid black;">At Completion</th>
        </tr>
        <tr style="background: #C6F1E7">
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Base Line</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous (EV) Allowable</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Previous Variance</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Todate Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Allowable (EV) Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Todate Cost Variance</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Remaining Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">at Completion Cost</th>
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">at Completion Cost Variance</th>
            {{--<th class="col-xs-1" style="border: 2px solid black;text-align: center">Concern</th>--}}
        </tr>

        </thead>
        <tbody>
        @foreach($resourceTypes as $id => $value)
            @php
                $typePreviousData = $previousData[$id] ?? [];
                $typeToDateData = $toDateData[$id] ?? [];
            @endphp
            <tr>
                <td style="border: 2px solid black;text-align: left">{{$value}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['budget_cost']??0,2) }}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typePreviousData['previous_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typePreviousData['previous_allowable']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typePreviousData['previous_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['to_date_cost']??0, 2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['ev']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if(($typeToDateData['to_date_var'] ?? 0) < 0) color: red; @endif">{{number_format($typeToDateData['to_date_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['remaining_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData['completion_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if(($typeToDateData['completion_cost_var']??0)<0) color: red; @endif">{{number_format($typeToDateData['completion_cost_var']??0,2)}}</td>
                {{--<td>--}}
                {{--<a  href="#" class="btn btn-primary btn-lg concern-btn"--}}
                {{--title="{{$value['name']}}"--}}
                {{--data-json="{{json_encode($value)}}">--}}
                {{--<i class="fa fa-pencil-square-o " aria-hidden="true"></i>--}}
                {{--</a>--}}
                {{--</td>--}}
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background: #F0FFF3">
            <th class="col-xs-1" style="border: 2px solid black;text-align: center">Total</th>
            <td style="border: 2px solid black;text-align: center;">{{number_format($toDateData->sum('budget_cost'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($previousData->sum('previous_cost'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($previousData->sum('previous_allowable'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($previousData->sum('previous_var'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('to_date_cost'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('ev'))}}</td>
            <td style="border: 2px solid black;text-align: center;@if($toDateData->sum('to_date_var') <0) color: red; @endif">{{number_format($toDateData->sum('to_date_var'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('remaining_cost'))}}</td>
            <td style="border: 2px solid black;text-align: center">{{number_format($toDateData->sum('completion_cost'))}}</td>
            <td style="border: 2px solid black;text-align: center; @if($toDateData->sum('completion_cost_var')<0) color: red; @endif">{{number_format($toDateData->sum('completion_cost_var'))}}</td>

        </tr>
        </tfoot>
    </table>
</section>
