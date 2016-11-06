@extends('layouts.app')

@section('header')
    <h2 class="panel-title">Project - {{$project->name}}</h2>

    <form action="{{ route('project.destroy', $project)}}" class="pull-right" method="post">
        {{csrf_field()}} {{method_field('delete')}}

        <a href="{{ route('project.edit', $project)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>
            Edit</a>
        <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        <a href="{{ route('project.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')
    @if (trim($project->description))
        <div class="panel panel-default">
            <div class="panel-body">
                {!! nl2br(e($project->description)) !!}
            </div>
        </div>
    @endif

    <div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default wbs-panel">
            <div class="panel-header">
                <div class="btn-toolbar">
                    <a href="/wbs-level/add?wbs=@{{selected.id}}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    <a href="/wbs-level/@{{selected.id}}/edit" class="btn btn-default"><i class="fa fa-edit"></i></a>
                    @can('wipe')
                        <a href="" class="btn btn-danger" title="Delete all"><i class="fa fa-trash"></i></a>
                    @endcan
                </div>
            </div>

            <div class="panel-body wbs-tree-container">
                
            </div>
        </div>
    </div>
    </div>
@endsection

@section('old')
    <ul class="nav nav-tabs">
        <li><a href="#wbs-structure" data-toggle="tab">WBS</a></li>
        <li><a href="#quantity-survey" data-toggle="tab">Quantity Survey</a></li>
        <li><a href="#breakdown" data-toggle="tab">Breakdown</a></li>
        <li><a href="#resources" data-toggle="tab">Resources</a></li>
        <li><a href="#productivity" data-toggle="tab">Productivity</a></li>
        <li><a href="#boq" data-toggle="tab">BOQs</a></li>
        <li><a href="#report" data-toggle="tab">Reports</a></li>
    </ul>

    <div class="tab-content">
        <section class="tab-pane" id="wbs-structure">
            @include('project.tabs._wbs')
        </section>

        <section class="tab-pane" id="quantity-survey">
            @include('project.tabs._quantity-survey')
        </section>

        <section class="tab-pane" id="breakdown">
            @include('project.tabs._breakdown')
        </section>

        <section class="tab-pane" id="resources">
            @include('project.tabs._resources')
        </section>

        <section class="tab-pane" id="productivity">
            @include('project.tabs._productivity')
        </section>
        <section class="tab-pane" id="boq">
            @include('project.tabs._boq')
        </section>

        <section class="tab-pane" id="report">
            @include('project.tabs._report',$project)
        </section>
    </div>
@stop

@section('javascript')
    <script>
        (function (w, d, $) {
            $(function () {
                $('.nav-tabs').on('click', 'a', function () {
                    window.location.hash = $(this).attr('href');
                });

                $(w).on('hashchange', function () {
                    var element = $('a[href="' + window.location.hash + '"]');
                    if (element.length) {
                        element.tab('show');
                    }
                });

                if (window.location.hash && $('a[href="' + window.location.hash + '"]').length) {
                    $('a[href="' + window.location.hash + '"]').tab('show');
                } else {
                    $('.nav-tabs a').first().tab('show');
                }

                var editResourceModal = $('#EditResourceModal');
                var modalContent = editResourceModal.find('.modal-body');
                $('.in-iframe').on('click', function (e) {
                    e.preventDefault();
                    modalContent.html('<iframe src="' + this.href + '" width="100%" height="100%" border="0" frameborder="0" style="border: none"></iframe>');
                    editResourceModal.modal();
                });
            });

        }(window, document, jQuery))

    </script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection