@extends('layouts.' . (request('iframe') ? 'iframe' : 'app'))

@section('title', 'Import Breakdowns')

@section('header')
    <div class="display-flex">
        <h3 class="flex">Import Breakdowns</h3>

        <a href="{{route('project.budget', $project)}}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back to Project
        </a>
    </div>
@endsection

@section('body')
<div class="row">
    <form method="post" enctype="multipart/form-data" class="col-sm-6"
            action="{{route('breakdowns.postImport', ['project' => $project, 'iframe' => request('iframe')])}}">

        {{csrf_field()}}

        <div class="form-group {{$errors->first('file', 'has-error')}}">
            <label for="file" class="control-label">Upload File</label>
            <p>Please <a href="/files/templates/easy-upload.xlsx">click here</a> to download template file</p>
            <div class="panel panel-success dropzone">
                <div class="panel-body drop-panel display-flex">
                    <div class="zone-wraper">
                        <h4 class="text-center">
                            Please click here or drag a file to upload
                        </h4>

                        <p class="text-center text-muted filename">&nbsp;</p>
                    </div>
                </div>
                <input type="file" name="file" id="file" class="form-control upload">
            </div>

            {!! $errors->first('file', '<div class="help-block">:message</div>') !!}
        </div>

        <div class="form-group">
            <button class="btn btn-primary"><i class="fa fa-check"></i> Submit</button>
        </div>
    </form>

    <div class="col-sm-9">
        <div class="alert alert-info">
            <h4>Please notice that the following rules will apply:</h4>
            <ul>
                <li>WBS Code must be found in project</li>
                <li>Breakdown template must be imported in project</li>
                <li>A QS for cost account must be found under selected WBS or one of its parents</li>
            </ul>
        </div>
    </div>
</div>


@endsection

@section('css')
    <style>
        .dropzone {
            position: relative;
        }

        .dropzone .upload {
            position: absolute;
            z-index: 60;
            opacity: 0;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: auto;
        }
    </style>
@endsection

@section('javascript')
    <script>
        $('.upload').on('change', function() {
            let tokens = this.value.split(/[\\\/]/);
            let filename = tokens[tokens.length - 1] || '';

            $(this).parent('.dropzone').find('.filename').text(filename);
        }).on('dragover', function (ev) {
            $(this).parent('.dropzone').find('.drop-panel').addClass('bg-success').css({opacity: 0.7});
        }).on('dragleave dragexit drop', function() {
            $(this).parent('.dropzone').find('.drop-panel').removeClass('bg-success').css({opacity: 1});
        }).change();
    </script>
@endsection