@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Import man days &mdash; {{$project->name}} &mdash; {{$period->name}}</h2>

        <div class="text-right">
            <a href="{{route('cost-man-days.export', $project)}}" class="btn btn-success btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>
            <a href="{{route('project.productivity-index-report', $project)}}" class="btn btn-info btn-sm">
                <i class="fa fa-chevron-left"></i> Report
            </a>

            <a href="{{route('project.cost-control', $project)}}" class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i> Project
            </a>
        </div>
    </div>
@endsection

@section('body')
<div class="row">
    <form action="" class="col-sm-9" method="post" enctype="multipart/form-data">
        {{csrf_field()}}

        <div class="form-group {{$errors->first('file', 'has-error')}}">
            <label for="file-input">Upload File</label>

            <div class="alert alert-success dropbox">
                <input type="file" id="file-input" name="file" class="form-control">
                <p class="lead text-center">
                    <strong><i class="fa fa-cloud-upload"></i> Please click here or drag and drop a file</strong><br><br>
                    <span class="filename">&nbsp;</span>
                </p>
            </div>

            {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
        </div>

        <div class="alert alert-info">
            <h4><i class="fa fa-info-circle"></i> <strong>Please notice:</strong></h4>
            <ul>
                <li><strong>Please <a href="/files/templates/cost_man_days.xlsx">click here</a> to download template file</strong></li>
                <li><strong>Values uploaded will override current data</strong></li>
                <li><strong>Please <a href="#">click here</a> to download current data</strong></li>
            </ul>
        </div>
    </form>
</div>

@endsection

@section('css')
    <style>
        .dropbox {
            min-height: 200px;
            position: relative;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .dropbox .form-control {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: block;
            opacity: 0;
            height: 100%;
        }
    </style>
@endsection

@section('javascript')
    <script>
        $(function() {
            $('.dropbox').on('dragenter', function() {
                $(this).removeClass('alert-success').addClass('alert-warning');
            }).on('dragleave drop', function () {
                $(this).removeClass('alert-warning').addClass('alert-success');
            }).find('.form-control').on('change', function() {
                $(this).closest('.dropbox').find('.filename').text(this.value);
            }).change();
        });
    </script>
@endsection