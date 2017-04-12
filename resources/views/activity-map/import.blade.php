@extends('layouts.app')

@section('header')
    <h2>{{$project->name}} &mdash; Import Activity Map</h2>

    <div class="pull-right">
        <a href="{{route('activity_mapping.export',$project)}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
        <a href="#DeleteActivityModal" class="btn btn-sm btn-warning" data-toggle="modal"><i class="fa fa-trash"></i>
            Delete Mapping</a>
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm"><i
                    class="fa fa-chevron-left"></i> Back</a>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="DeleteActivityModal">
        <div class="modal-dialog">
            <form action="{{route('activity-map.delete', $project)}}" class="modal-content" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete activity mapping</h4>
                </div>
                <div class="modal-body">
                    {{csrf_field()}}
                    {{method_field('delete')}}
                    <p class="lead">Are you sure you want to delete activity mapping for this project?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['activity-map.post-import', $project], 'files' => true])}}

            <p class="text-info">
                <i class="fa fa-download"></i> Please <a href="{{asset('/files/templates/activity-map.xlsx')}}">click
                    here</a> to download a sample template
            </p>

            <div class="form-group {{$errors->first('file', 'has-error')}}">
                {{Form::label('file', null, ['class' => 'control-label'])}}
                {{Form::file('file', ['class' => 'form-control'])}}
                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

            {{Form::close()}}

        </div>
    </div>
@endsection