@extends('layouts.app')

@section('header')
    <h3>{{$changeRequest->project->name}}</h3>

    <div class="pull-right">
        <a href="{{route('project.budget', $changeRequest->project)}}" class="btn btn-sm btn-default"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>
@endsection

@section('body')
    <table class="table table-condensed table-bordered table-striped table-responsive">
        <thead>
        <tr class="alert-success">
            <th class="col-xs-3">WBS</th>
            <th class="col-xs-3">Activity</th>
            <th class="col-xs-3">Resource</th>
            <th class="col-xs-3">Comment</th>
        </tr>

        <tr>
            <th>{{$changeRequest->wbs->code ?? '' }} &mdash; {{$changeRequest->wbs->name ?? '' }}</th>
            <th>
                {{$changeRequest->activity->code ?? '' }} &mdash; {{$changeRequest->activity->name  ?? ''}}</th>
            <th>
                {{$changeRequest->resourse->code ?? '' }} &mdash; {{$changeRequest->resourse->name ?? '' }}</th>
            <th>
                {{$changeRequest->description ?? ''}}
            </th>
        </tr>
        </thead>

    </table>

    <table class="table table-condensed table-bordered table-striped table-responsive">
        <thead>
        <tr class="alert-info">
            <th class="col-xs-3">Created By</th>
            <th class="col-xs-3">Assigned To</th>
            <th class="col-xs-3">Proposed Unit Price</th>
            <th class="col-xs-3">Proposed Qty</th>

        </tr>

        <tr>
            <th>{{$changeRequest->created_by()->first()->name ?? ''}}</th>
            <th>{{$changeRequest->assigned_to()->first()->name ?? ''}}</th>
            <th>{{$changeRequest->unit_price ?? 0}}</th>
            <th>
                {{$changeRequest->qty ?? 0}}
            </th>
        </tr>
        </thead>

    </table>
    @if(!$changeRequest->closed && can('reassigned_request',$changeRequest))
        <div class="row">
            <div class="col-md-3">
                <form action="{{route('project.change-request.reassgin',[$changeRequest->project,$changeRequest])}}"
                      method="post">
                    {{csrf_field()}} {{method_field('post')}}
                    <div class="form-group {{$errors->first('assigned_to', 'has-error')}}">
                        {{ Form::label('assigned_to', 'Reassign To', ['class' => 'control-label']) }}
                        {{ Form::select('assigned_to', App\User::options(), null, ['class' => 'form-control']) }}
                        {!! $errors->first('assigned_to', '<div class="help-block">:message</div>') !!}
                    </div>

                    <div class="form-group {{$errors->first('due_date', 'has-error')}}">
                        {{ Form::label('due_date', 'Due date', ['class' => 'control-label']) }}
                        {{ Form::date('due_date', null, ['class' => 'form-control']) }}
                        {!! $errors->first('due_date', '<div class="help-block">:message</div>') !!}
                    </div>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </form>
            </div>
        </div>
    @endif
    <br>
    @if(!$changeRequest->closed && can('close_request',$changeRequest))
        <div class="row">
            <div class="col-md-12">
                <form action="{{route('project.change-request.close',[$changeRequest->project,$changeRequest])}}"
                      method="post">
                    {{csrf_field()}} {{method_field('post')}}
                    <div class="form-group {{$errors->first('closed', 'has-error')}}">
                        {{ Form::label('close_note', 'Close Note', ['class' => 'control-label']) }}
                        {{ Form::textarea('close_note', null, ['class' => 'form-control']) }}
                        {!! $errors->first('close_note', '<div class="help-block">:message</div>') !!}
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>


            </div>
        </div>
    @endif
@endsection