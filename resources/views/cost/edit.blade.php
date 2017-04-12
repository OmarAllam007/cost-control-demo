@extends('layouts.' . (request('iframe')? 'iframe' : 'app'))

@section('header')
    <h2>Edit Resource</h2>
@endsection

@section('body')
    {{Form::model($cost_shadow, ['route' => ['cost.update', $cost_shadow], 'class' => 'row'])}}

    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group {{$errors->first('remaining_qty', 'has-error')}}">
                    {{Form::label('remaining_qty', 'Remaining Qty', ['class' => 'control-label'])}}
                    {{Form::text('remaining_qty', null, ['class' => 'form-control', 'id' => 'RemainingQty'])}}
                    {!! $errors->first('remaining_qty', '<div class="help-block">:message</div>') !!}
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group {{$errors->first('remaining_unit_price', 'has-error')}}">
                    {{Form::label('remaining_unit_price', 'Remaining Unit Price', ['class' => 'control-label'])}}
                    {{Form::text('remaining_unit_price', null, ['class' => 'form-control', 'id' => 'RemainingUnitPrice'])}}
                    {!! $errors->first('remaining_unit_price', '<div class="help-block">:message</div>') !!}
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group {{$errors->first('allowable_ev_cost', 'has-error')}}">
                    {{Form::label('allowable_ev_cost', 'Allowable To Date', ['class' => 'control-label', 'for' => 'AllowableEvCost'])}}
                    {{Form::text('allowable_ev_cost', null, ['class' => 'form-control', 'id' => 'AllowableEvCost'])}}
                    {!! $errors->first('allowable_ev_cost', '<div class="help-block">:message</div>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{$errors->first('budget[progress]', 'has-error')}}">
                    {{Form::label('budget[progress]', 'Progress', ['class' => 'control-label'])}}
                    {{Form::text('budget[progress]', null, ['class' => 'form-control', 'id' => 'RemainingCost'])}}
                    {!! $errors->first('budget[progress]', '<div class="help-block">:message</div>') !!}
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group {{$errors->first('budget[status]', 'has-error')}}">
                    {{Form::label('budget[status]', 'Status', ['class' => 'control-label'])}}
                    {{Form::select('budget[status]', config('app.cost_status'), null, ['class' => 'form-control', 'id' => 'RemainingCost'])}}
                    {!! $errors->first('budget[status]', '<div class="help-block">:message</div>') !!}
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <tbody>
            <tr>
                <th class="col-sm-3">Period</th>
                <td>{{$cost_shadow->period->name}}</td>
            </tr>
            <tr>
                <th class="col-sm-3">WBS</th>
                <td>{{$cost_shadow->budget->wbs->path}} <small>({{$cost_shadow->budget->wbs->code}})</small></td>
            </tr>
            <tr>
                <th>Activity</th>
                <td>{{$cost_shadow->budget->activity}}</td>
            </tr>

            <tr>
                <th>Cost Account</th>
                <td>{{$cost_shadow->budget->cost_account}}</td>
            </tr>

            <tr>
                <th>Resource Code</th>
                <td>{{$cost_shadow->budget->resource_code}}</td>
            </tr>

            <tr>
                <th>Resource Name</th>
                <td>{{$cost_shadow->budget->resource_name}}</td>
            </tr>

            <tr>
                <th>Resource Type</th>
                <td>{{$cost_shadow->budget->resource_type}}</td>
            </tr>

            <tr>
                <th>Budget Cost</th>
                <td>{{number_format($cost_shadow->budget->budget_cost, 2)}}</td>
            </tr>

            <tr>
                <th>Budget Unit</th>
                <td>{{number_format($cost_shadow->budget->budget_unit, 2)}}</td>
            </tr>

            <tr>
                <th>Budget Unit Price</th>
                <td>{{number_format($cost_shadow->budget->unit_price, 2)}}</td>
            </tr>

            <tr>
                <th>Budget U.O.M</th>
                <td>{{$cost_shadow->budget->measure_unit}}</td>
            </tr>
            </tbody>
        </table>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
        </div>
    </div>

    {{Form::close()}}
@endsection