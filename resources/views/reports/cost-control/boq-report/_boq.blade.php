<tr class="level-{{$depth}} boq {{slug($key)}} hidden">
    <td><i class="fa fa-caret-right"></i> {{$boq['cost_account']}}</td>
    <td>{{$boq['description']}}</td>
    <td>{{number_format($boq['dry_price'], 2)}}</td>
    <td>{{number_format($boq['boq_price'], 2)}}</td>
    <td>{{number_format($boq['budget_unit_rate'], 2)}}</td>
    <td>{{number_format($boq['boq_qty'], 2)}}</td>
    <td>{{number_format($boq['budget_qty'], 2)}}</td>
    <td>{{number_format($boq['physical_qty'], 2)}}</td>
    <td>{{number_format($boq['dry_cost'], 2)}}</td>
    <td>{{number_format($boq['boq_cost'], 2)}}</td>
    <td>{{number_format($boq['budget_cost'], 2)}}</td>
    <td>{{number_format($boq['to_date_cost'], 2)}}</td>
    <td>{{number_format($boq['to_date_allowable'], 2)}}</td>
    <td class="{{$boq['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($boq['to_date_var'], 2)}}</td>
    <td>{{number_format($boq['remaining_cost'], 2)}}</td>
    <td>{{number_format($boq['at_completion_cost'], 2)}}</td>
    <td class="{{$boq['at_completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($boq['at_completion_var'], 2)}}</td>
</tr>