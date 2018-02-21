@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Modify Productivity</h2>
        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Back to Project</a>
    </div>
@stop

@section('body')
    <form action="" method="post" enctype="multipart/form-data">
        {{csrf_field()}}

        <div class="row">
            <div class="form-group {{$errors->first('file', 'has-error')}} col-sm-6">
                <label for="file">Upload file</label>
                <div class="file-container alert alert-success">
                    <p class="lead">Click to select a file or drop a file here</p>
                    <p class="file-name"></p>
                    <input type="file" name="file" id="file" class="">
                </div>

                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Upload</button>
        </div>

        <p>&nbsp;</p>

        <div class="alert alert-info">
            <h5><i class="fa fa-info-circle"></i> Please Notice</h5>
            <ul>
                <li>Please use the same template as exported from project productivity</li>
                <li>Only reduction factor will be changed</li>
            </ul>
        </div>
    </form>
    {{ Form::close() }}
@stop

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