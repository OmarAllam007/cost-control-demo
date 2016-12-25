@extends('layouts.' . (request('print')? 'print' : 'app'))
@if(request('all'))
    @include('reports.all._high_priority_materials')
@endif
@section('header')
    <h2>Significant Materials</h2>

@endsection

@section('body')
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
        @foreach($data as $key=>$value)
        <tr>
            <td>{{$key}}</td>
            <td>{{$value['budget_cost'] ?? 0 }}</td>
            <td>{{$value['previous_cost']?? 0}}</td>
            <td>{{$value['previous_allowable'] ?? 0 }}</td>
            <td>{{$value['previous_variance'] ?? 0 }}</td>
            <td>{{$value['to_date_cost'] ?? 0 }}</td>
            <td>{{$value['allowable_ev_cost'] ?? 0 }}</td>
            <td>{{$value['to_date_variance'] ?? 0 }}</td>
            <td>{{$value['remaining_cost'] ?? 0 }}</td>
            <td>{{$value['at_completion_cost'] ?? 0 }}</td>
            <td>{{$value['cost_variance'] ?? 0 }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>




    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Description</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <textarea class="texta form-control" title="new"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

@stop
