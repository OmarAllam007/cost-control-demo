@extends('layouts.app')

@section('title', 'Update Progress')

@section('header')
    <div class="display-flex">
        <h2 class="flex">{{$project->name}} &mdash; Update Progress</h2>
        <a href="{{route('project.cost-control', $project)}}" class="btn btn-default">
            <i class="fa fa-chevron-left"></i> Back to Project
        </a>
    </div>
@endsection

@section('body')
    <form action="" method="post" enctype="multipart/form-data" class="row">
        {{csrf_field()}}
        {{method_field('put')}}

        <div class="col-md-6 col-sm-9">
            <article class="form-group {{$errors->first('file', 'has-error')}}">
                <label for="file">Upload file</label>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>
                        Please <a href="{{url('files/templates/update_progress_template.xlsx')}}">click here</a>
                        to download sample template
                    </strong>
                </div>

                <div class="file-container alert alert-success">
                    <p class="lead">Click to select a file or drop a file here</p>
                    <p class="file-name"></p>
                    <input type="file" name="file">
                </div>

                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </article>

            <div class="form-group">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Update
                </button>
            </div>
        </div>
    </form>
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