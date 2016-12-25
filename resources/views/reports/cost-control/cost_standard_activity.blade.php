@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif
@section('header')
    <h2>Significant Materials</h2>
@endsection
@section('body')
    <ul class="list-unstyled tree">
        @foreach($data as $key=>$value)
            <li>

                <div class="tree--item">
                    <a href="#col" {{$value['division_id']}} class="tree--item--label" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i>
                        {{$key}} </a>
                </div>
                @foreach($value['activities']  as $activityKey=>$actvalue)
                    <div class="collapse" id="col"{{$value['division_id']}}>
                        <a href="#data" {{$actvalue['activity_id']}} class="tree--child" data-toggle="collapse"><i class="fa fa-chevron-circle-right"></i>
                            {{$activityKey}} </a>
                    </div>
                    <br>
                    <article id="data" {{$actvalue['activity_id']}} class="tree--child collapse">
                        <table class="table table-condensed">
                            <thead>
                            <tr class="tbl-children-division">
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
                            <tr>
                                <td >{{number_format($actvalue['budget_cost']??0,2) }}</td>
                                <td >{{number_format($actvalue['previous__date_cost']??0,2)}}</td>
                                <td >{{number_format($actvalue['previous_allowable_ev_cost']??0,2)}}</td>
                                <td >{{number_format($actvalue['previous_to_date_var']??0,2)}}</td>
                                <td >{{number_format($actvalue['to_date_cost']??0,2)}}</td>
                                <td >{{number_format($actvalue['to_date_allowable_ev_cost']??0,2)}}</td>
                                <td >{{number_format($actvalue['to_date_var']??0,2)}}</td>
                                <td >{{number_format($actvalue['remain_cost']??0,2)}}</td>
                                <td >{{number_format($actvalue['at_completion_cost']??0,2)}}</td>
                                <td >{{number_format($actvalue['cost_variance']??0,2)}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </article>

                @endforeach
                @endforeach
            </li>
    </ul>
@stop
