<tr class="activity level-{{$depth}} {{slug($key)}} {{$depth > 1? 'hidden' : ''}}">
    <td><a href="#" class="open-level" data-target="{{slug($key .'-'.$name)}}"><i class="fa fa-plus-square-o"></i> {{$activity['name']}}</a></td>
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


@foreach($activity['resources'] as $resource)
    <tr class="resource level-{{$depth + 1}} {{slug($key .'-'.$name)}} hidden">
        <td><i class="fa fa-angle-right"></i> {{$resource->resource_name}}</td>
        <td>{{number_format($resource['budget_cost'], 2)}}</td>

        <td>{{number_format($resource['prev_cost'], 2)}}</td>
        <td>{{number_format($resource['prev_allowable'], 2)}}</td>
        <td class="{{$resource['prev_cost_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($resource['prev_cost_var'], 2)}}</td>

        <td>{{number_format($resource['to_date_cost'], 2)}}</td>
        <td>{{number_format($resource['to_date_allowable'], 2)}}</td>
        <td class="{{$resource['to_date_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($resource['to_date_var'], 2)}}</td>

        <td>{{number_format($resource['remaining_cost'], 2)}}</td>

        <td>{{number_format($resource['completion_cost'], 2)}}</td>
        <td class="{{$resource['completion_var'] < 0? 'text-danger' : 'text-success'}}">{{number_format($resource['completion_var'], 2)}}</td>
    </tr>
@endforeach