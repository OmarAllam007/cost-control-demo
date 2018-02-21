@extends('layouts.app')

@section('title', '')

@section('header')
    <div class="display-flex">
        <h2 class="flex">
            @if ($project)
                {{ $project->name }} &mdash;
            @endif
            Modify Breakdown Templates
        </h2>

        @if ($project)
            <a href="{{ route('project.budget', $project) }}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back to project</a>
        @else
            <a href="{{ route('breakdown-template.index') }}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back to templates</a>
        @endif
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
            <ul>
                <li>
                    You can export breakdown templates 
                    <a href="{{ route('breakdown-template.export') . ($project? "?project_id=$project->id" : '') }}">from here</a>
                </li>
                <li>You can only modify resource code, equation, important resource</li>
                @if ($project)
                    <li class="text-danger">Updates will be applied to all resources in all breakdowns</li>
                @endif
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