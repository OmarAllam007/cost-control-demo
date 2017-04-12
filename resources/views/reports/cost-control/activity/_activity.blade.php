<tr class="activity level-{{$depth}} {{slug($key)}} {{$depth > 1? 'hidden' : ''}}">
    <td><i class="fa fa-caret-right"></i> {{$name}}</td>
    <td>{{number_format($activity['budget_cost'], 2)}}</td>

    <td>{{number_format($activity['prev_cost'], 2)}}</td>
    <td>{{number_format($activity['prev_allowable'], 2)}}</td>
    <td class="{{$activity['prev_cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($activity['prev_cost_var'], 2)}}</td>

    <td>{{number_format($activity['to_date_cost'], 2)}}</td>
    <td>{{number_format($activity['to_date_allowable'], 2)}}</td>
    <td class="{{$activity['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($activity['to_date_var'], 2)}}</td>

    <td>{{number_format($activity['remaining_cost'], 2)}}</td>

    <td>{{number_format($activity['completion_cost'], 2)}}</td>
    <td class="{{$activity['completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($activity['completion_var'], 2)}}</td>
</tr>