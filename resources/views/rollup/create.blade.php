@extends(request()->exists('iframe') ? 'layouts.iframe' : 'layouts.app')

@section('title', 'Rollup resources')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Rollup resources</h2>

        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
    {{dump($errors->all())}}
    <div class="row mb-20">
        <form action="{{ route('rollup.store', $key) . (request()->exists('iframe')? '?iframe=1' : '') }}" method="post" class="col-sm-6 br-1">
            {{csrf_field()}}

            <div class="form-group {{$errors->first('code', 'has-error')}}">
                <label for="resourceCode" class="control-label">Code</label>
                <input type="text" name="code" id="resourceCode" class="form-control" value="{{old('code', $code)}}" autofocus>
                {!! $errors->first('code', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group {{$errors->first('name', 'has-error')}}">
                <label for="resourceName" class="control-label">Name</label>
                <input type="text" name="name" id="resourceName" class="form-control" value="{{old('name', $name)}}">
                {!! $errors->first('name', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group {{$errors->first('type', 'has-error')}}">
                <label for="resourceType" class="control-label">Resource Type</label>
                <select name="type" id="resourceType" class="form-control">
                    <option value="">-- Select Type --</option>
                    @foreach($resourceTypes as $id => $name)
                        <option value="{{$id}}" {{old('type', $type) == $id? 'selected' : ''}}>{{$name}}</option>
                    @endforeach
                </select>
                {!! $errors->first('type', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group {{$errors->first('qty', 'has-error')}}">
                <label for="resourceQty" class="control-label">Budget Quantity</label>
                <input type="text" name="qty" id="resourceQty" class="form-control" value="{{old('qty', 1)}}">
                {!! $errors->first('qty', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group {{$errors->first('progress', 'has-error')}}">
                <label for="resourceProgress" class="control-label">Progress</label>
                <div class="input-group">
                    <input type="text" name="progress" id="resourceProgress" class="form-control" value="{{old('progress', $progress)}}">
                    <span class="input-group-addon">%</span>
                </div>
                {!! $errors->first('progress', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group mb-0">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
            </div>
        </form>

        <div class="col-sm-6">
            <dl>
                <dd>Project</dd>
                <dt>{{$project->name}}</dt>
            </dl>

            <dl>
                <dd>WBS</dd>
                <dt>{{$wbsLevel->path}} ({{$wbsLevel->code}})</dt>
            </dl>

            <dl>
                <dd>Activity</dd>
                <dt>{{$stdActivity->name}}</dt>
            </dl>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">Resources</h4>
        </div>

        <table class="table table-condensed table-striped table-hover table-bordered">
            <thead>
            <tr>
                <th>Cost Account</th>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Budget Qty</th>
                <th>U.O.M</th>
                <th>Budget Cost</th>
                <th>To Date Cost</th>
                <th>Progress</th>
            </tr>
            </thead>
            <tbody>
            @foreach($resources as $resource)
                <tr>
                    <th>{{$resource->cost_account}}</th>
                    <th>{{$resource->resource_code}}</th>
                    <th>{{$resource->resource_name}}</th>
                    <th>{{$resource->resource_type}}</th>
                    <th>{{number_format($resource->budget_qty, 2)}}</th>
                    <th>{{$resource->measure_unit}}</th>
                    <th>{{number_format($resource->budget_cost, 2)}}</th>
                    <th>{{number_format($resource->to_date_cost, 2)}}</th>
                    <th>{{number_format($resource->progress, 1)}}%</th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('css')
    <style>
        .br-1 {
            border-right: 1px solid #e5e5e5;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mb-0 {
            margin-bottom: 0;
        }
    </style>
@endsection