@extends('layouts.app')

@section('header')
    <div class="display-flex">
        <h2 class="flex">Modify Qty Survey &mdash; {{$project->name}}</h2>
        
        <div class="btn-tollbar">
            <a href="{{route('survey.export', $project)}}" class="btn btn-sm btn-primary">
                <i class="fa fa-cloud-download"></i> Export Qty Survey
            </a>

            <a href="{{route('project.budget', $project)}}" class="btn btn-sm btn-default">
                <i class="fa fa-chevron-left"></i> Back to project
            </a>
        </div>
    </div>
@endsection

@section('body')
    <form action="" method="post" class="row" enctype="multipart/form-data">
        {{csrf_field()}}
        {{method_field('put')}}

        <section class="col-sm-9">

            <div class="form-group {{$errors->first('file', 'has-error')}}">
                <div class="file-container bg-info">
                    <label for="file">Drop a file here or click to choose a file</label>
                    <p class="file-name"></p>
                    <input type="file" name="file" id="file">
                </div>

                {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
            </div>

            <div class="form-group">
                <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
            </div>
        </section>
    </form>
@endsection

@section('javascript')
    <script>
        $(function() {
            $('#file').change(function () {
                $('.file-name').text(this.value);
            }).change();

            $('.file-container').on('dragover', function() {
                $(this).addClass('bg-success').removeClass('bg-info');
            }).on('dragleave dragstop dragend drop', function() {
                $(this).addClass('bg-info').removeClass('bg-success');
            });
        });
    </script>
@endsection