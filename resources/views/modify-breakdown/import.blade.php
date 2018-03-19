@extends('layouts.app')

@section('title')
    {{ $project->name }} &mdash; Modify Breakdown
@endsection

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            {{ $project->name }} &mdash; Modify Breakdown
        </h2>

        <div class="btn-toolbar">
            <a href="{{route('project.breakdown.export', $project)}}" class="btn btn-sm btn-success">
                <i class="fa fa-cloud-download"></i> Export
            </a>

            <a href="{{ route('project.budget', $project) }}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back to project</a>
        </div>
    </div>
@endsection

@section('body')
    <div class="row">
        <form action="" method="post" class="col-sm-9 col-md-6" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('put') }}

            <article class="form-group {{$errors->first('file', 'has-error')}}">
                <label for="file">Upload file</label>
                <div class="file-container alert alert-success">
                    <p class="lead">Click to select a file or drop a file here</p>
                    <p class="file-name"></p>
                    <input type="file" name="file" id="file" class="">
                </div>

                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </article>

            <div class="form-group">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
            </div>

            <div class="alert alert-info">
                <p><strong><i class="fa fa-info-circle"></i> Please notice</strong></p>
                <ul class="list-unstyled">
                    <li>
                        <i class="fa fa-fw fa-angle-right"></i>
                        You can export project breakdowns for modify
                        <a href="{{route('project.breakdown.export', $project)}}">from here</a>
                    </li>
                    <li><i class="fa fa-fw fa-angle-right"></i> Changes in WBS code, activity code, activity cost account, and resource name will have no effect</li>
                    <li class="text-danger"><strong><i class="fa fa-fw fa-warning"></i> Please do not change APP_ID. Changes in this field is dangerous</strong></li>
                </ul>
            </div>

        </form>
    </div>
@endsection

@section('javascript')
    <script>
        $(function() {
            $('.file-container').on('change', 'input', e => {
                const tokens = e.target.value.split(/[\\\/]/);
                e.target.parentNode.querySelector('.file-name').innerHTML = tokens[tokens.length - 1];
            }).on('dragenter', e => {
                $('.file-container').addClass('alert-warning').removeClass('alert-success');
            }).on('dragleave drop', e => {
                $('.file-container').removeClass('alert-warning').addClass('alert-success');
            }).find('input:file').change();
        });
    </script>
@endsection