@extends('layouts.app')

@section('header')
    <h2>
        Import equivalent resource codes
        @if ($project_id = request('project'))
        &mdash; {{App\Project::find($project_id)->name}}
        @endif
    </h2>

    <div class="pull-right">
        <a href="{{route('resource_mapping.export',$project_id)}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>

        <a href="#DeleteResourceCodes" class="btn btn-warning btn-sm" data-toggle="modal"><i class="fa fa-trash"></i> Delete Mapping</a>

        @if ($project_id)
            <a href="{{route('project.cost-control', $project_id)}}" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        @else
            <a href="{{route('resources.index')}}" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Back
            </a>
        @endif
    </div>

    @if ($project_id)
    <div class="modal fade" tabindex="-1" role="dialog" id="DeleteResourceCodes">
        <div class="modal-dialog">
            <form action="{{route('resources.delete-codes', $project_id)}}" class="modal-content" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Delete resource mapping</h4>
                </div>
                <div class="modal-body">
                    {{csrf_field()}}
                    {{method_field('delete')}}
                    <p class="lead">Are you sure you want to delete resource mapping for this project?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6 col-sm-9">

            {{Form::open(['route' => ['resources.post-import-codes', 'project' => $project_id], 'files' => true])}}

            <p class="text-info">
                <i class="fa fa-download"></i> Please <a href="{{asset('/files/templates/resource-map.xlsx')}}">click
                    here</a> to download a sample template
            </p>

            <div class="form-group {{$errors->first('file', 'has-errors')}}">
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