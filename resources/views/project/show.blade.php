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

    <nav class="project-nav">
        <a href="#wbsArea" class="btn btn-outline btn-primary">WBS &amp; Activity</a>
        <a href="#Resources" class="btn btn-outline btn-primary">Resources</a>
        <a href="#Productivity" class="btn btn-outline btn-primary">Productivity</a>
        <a href="#Reports" class="btn btn-outline btn-success">Reports</a>
    </nav>

    <section id="wbsArea">
        <div class="row">
            <div class="col-sm-4">
                <aside class="panel panel-default wbs-panel">
                    <div class="panel-heading clearfix">
                        <h3 class="panel-title  pull-left">WBS</h3>
                        <div class="btn-toolbar pull-right">
                            <a href="/wbs-level/create?project={{$project->id}}&wbs=@{{selected}}" data-title="Add WBS Level" class="btn btn-sm btn-default"><i class="fa fa-plus"></i></a>
                            <a href="{{route('wbs-level.import', $project)}}" data-title="Import WBS" class="btn btn-sm btn-success in-iframe" title="import"><i class="fa fa-cloud-upload"></i></a>
                            <a href="/wbs-level/@{{selected}}/edit" class="btn btn-sm btn-primary" title="Edit" v-show="selected"><i class="fa fa-edit"></i></a>
                            @can('wipe')
                                <a href="" class="btn btn-sm btn-danger" title="Delete all"><i class="fa fa-trash"></i></a>
                            @endcan
                        </div>
                    </div>

                    <div class="panel-body wbs-tree-container">
                        @include('project.templates.wbs', compact('wbsTree'))
                    </div>
                </aside>
            </div>


            <div class="col-sm-8">
                <section id="wbs-display" v-show="selected">
                    <alert></alert>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#Breakdown">Resources</a></li>
                        <li><a href="#BOQ">BOQ</a></li>
                        <li><a href="#QtySurvey">Quantity Survey</a></li>
                    </ul>

                    <div class="tab-content">
                        <article class="tab-pane active" id="Breakdown">
                            @include('project.templates.breakdown')
                        </article>

                        <article class="tab-pane" id="BOQ">
                            @include('project.templates.boq')
                        </article>

                        <article class="tab-pane" id="QtySurvey">
                            @include('project.templates.qty-survey')
                        </article>
                    </div>
                </section>

                <div class="alert alert-info" v-else>
                    <i class="fa fa-info-circle"></i> Please select a WBS
                </div>
            </div>

        </div>
    </section>


    <div class="modal fade" tabindex="-1" id="IframeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span>
                    </button>
                    <h4 class="modal-title">Edit resource</h4>
                </div>
                <div class="modal-body iframe">

                </div>
            </div>
        </div>
    </div>
@endsection

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

                var iframeModal = $('#IframeModal');
                var modalContent = iframeModal.find('.modal-body');
                $('#wbsArea').on('click', '.in-iframe', function (e) {
                    e.preventDefault();
                    var href = this.href;
                    if (href.indexOf('?') < 0) {
                        href += '?iframe=1';
                    } else {
                        href += '&iframe=1';
                    }
                    modalContent.html('<iframe src="' + href + '" width="100%" height="100%" border="0" frameborder="0" style="border: none"></iframe>');
                    iframeModal.find('.modal-title').text($(this).data('title'));
                    iframeModal.modal();
                });
            });

        }(window, document, jQuery));

    </script>
    <script src="{{asset('/js/project.js')}}"></script>
@endsection