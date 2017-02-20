@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif
@section('header')
    <h2>{{$project->name}} - Significant Materials Report</h2>
    <style>
        .padding{
            padding-right: 300px;
        }
    </style>
@endsection

@section('body')
    <div class="row" style="margin-bottom: 10px;">
        <form action="{{route('cost.significant',$project)}}" class="form-inline col col-md-8" method="get">
            {{Form::select('period_id', \App\Period::where('project_id',$project->id)->where('is_open',0)->lists('name','id') ,Cache::has('period_id'.$project->id) ? Cache::get('period_id'.$project->id) : 'Select Period',  ['placeholder' => 'Choose a Period','class'=>'form-control padding'])}}
            {{Form::submit('Submit',['class'=>'form-control btn-success'],['class'=>'form-control btn-success'])}}
        </form>
        <br>
    </div>

    <ul class="list-unstyled tree">
        @foreach($data as $item)
            <li>
                <p class="blue-first-level">
                    <a href="#{{$item['resource_type_id']}}" data-toggle="collapse" style="color: white;text-decoration: none">
                        {{$item['resource_type']}}
                    </a>                </p>
                <article id="{{$item['resource_type_id']}}" class="collapse tree--child">
                    <table class="table table-condensed">
                        <thead>
                        <tr class="tbl-children-division">
                            <th class="col-xs-2">Row Labels</th>
                            <th class="col-xs-1">Base Line</th>
                            <th class="col-xs-1">Previous Cost</th>
                            <th class="col-xs-1">Previous allowable</th>
                            <th class="col-xs-1">Previous Variance</th>
                            <th class="col-xs-1">To Date Cost</th>
                            <th class="col-xs-1">Allowable (EV) Cost</th>
                            <th class="col-xs-1">To Date Variance</th>
                            <th class="col-xs-1">Remaining Cost</th>
                            <th class="col-xs-1">At Compeletion Cost</th>
                            <th class="col-xs-1">Cost Variance</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($item['resources'] as $key=>$value)
                            <tr>
                                <td>{{$key}}</td>
                                <td>{{number_format($value['budget_cost'] ?? 0 ,2)}}</td>
                                <td>{{number_format($value['previous_cost']?? 0,2)}}</td>
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
                </article>
            </li>
        @endforeach
    </ul>



@stop
