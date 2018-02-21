<h4 class="card-title section-header dark-cyan">Cost Summary</h4>
<section class="card-body">
    <table class="table table-condensed cost-summary-table">
        <thead>
        <tr style="border: 2px solid black;background: #8ed3d8;color: #000;">
            <th class="col-sm-2" rowspan="2" style="border: 2px solid black;text-align: center"></th>
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
                $typePreviousData = $previousData;
                $typeToDateData = $toDateData;
            @endphp
            <tr>
                <td style="border: 2px solid black;text-align: left">{{$value}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData[mb_strtolower($value)]['budget_cost']??0,2) }}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typePreviousData[mb_strtolower($value)]['previous_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typePreviousData[mb_strtolower($value)]['previous_allowable']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typePreviousData[mb_strtolower($value)]['previous_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData[mb_strtolower($value)]['to_date_cost']??0, 2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData[mb_strtolower($value)]['ev']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if(($typeToDateData['to_date_var'] ?? 0) < 0) color: red; @endif">{{number_format($typeToDateData[mb_strtolower($value)]['to_date_var']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData[mb_strtolower($value)]['remaining_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center">{{number_format($typeToDateData[mb_strtolower($value)]['completion_cost']??0,2)}}</td>
                <td style="border: 2px solid black;text-align: center; @if(($typeToDateData['completion_cost_var']??0)<0) color: red; @endif">{{number_format($typeToDateData[mb_strtolower($value)]['completion_cost_var']??0,2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background: #F0FFF3">

        </tr>
        </tfoot>
    </table>
</section>
