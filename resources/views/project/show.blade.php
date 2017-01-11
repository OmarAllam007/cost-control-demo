@extends('layouts.app')

@section('header')
    <h2 class="panel-title">Project - {{$project->name}}</h2>
    <div class="pull-right">
        @can('modify', $project)
            {{csrf_field()}} {{method_field('delete')}}
            <a href="{{ route('project.edit', $project)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
            <a href="#DeleteProjectModal" class="btn btn-sm btn-warning" data-toggle="modal"><i class="fa fa-trash-o"></i> Delete </a>
        @endcan

        <a href="{{ route('project.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </div>


@stop

@section('body')

    <nav class="project-nav">
        @can('budget', $project)
            <a href="#wbsArea" class="btn btn-sm btn-primary"><i class="fa fa-building-o"></i> WBS &amp; Activity</a>
            <a href="#ProjectResources" class="btn btn-sm btn-outline btn-info"><i class="fa fa-bullseye"></i> Resources</a>
            <a href="#ProjectProductivities" class="btn btn-sm btn-outline btn-info"><i class="fa fa-male"></i> Productivity</a>
            <a href="#ProjectTemplates" class="btn btn-sm btn-outline btn-violet"><i class="fa fa-magic"></i> Breakdown Templates</a>
        @endcan

        @can('reports', $project)
            <a href="#ReportsArea" class="btn btn-sm btn-outline btn-success"><i class="fa fa-line-chart"></i> Reports</a>
        @endcan
    </nav>
    <div id="projectArea" class="hidden">
        @can('budget', $project)
            @include('project.tabs.wbs-area')

            <article id="ProjectResources" class="project-tab">
                @include('project.templates.resources')
            </article>

            <article id="ProjectTemplates" class="project-tab">
            @include('project.templates.breakdown-template')
            </article>

            <article id="ProjectProductivities" class="project-tab">
            @include('project.templates.productivity')
            </article>
        @endcan

        @can('reports', $project)
            @include('project.tabs._report')
        @endcan
    </div>

    @include('project.templates.iframe-modal')

    @can('wipe')
        <div class="modal fade" id="DeleteBreakdownModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <form method="post" action="{{route('breakdownresources.deleteAllBreakdowns',$project)}}" class="modal-content">
                    {{csrf_field()}} {{method_field('delete')}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <h4 class="modal-title">Delete all breakdown</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete all breakdowns in the project?</div>
                        <input type="hidden" name="wipe" value="1">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger"><i class="fa fa-fw fa-trash"></i> Delete</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    @can('wipe')
        <div class="modal fade" id="DeleteProjectModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <form action="{{ route('project.destroy', $project) }}" method="post" class="modal-content">
                    {{csrf_field()}} {{method_field('delete')}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <h4 class="modal-title">Delete - {{$project->name}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete ( {{$project->name}} ) Project?</div>
                        <input type="hidden" name="wipe" value="1">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger"><i class="fa fa-fw fa-trash"></i> Delete</button>
                    </div>
                </form>
            </div>
        </div>


    @endcan


@endsection

@section('javascript')
    <script>
        (function (w, d, $) {
            $(function () {
                $('.project-tab').hide();
                $('#wbsArea').show();
                $('#projectArea').removeClass('hidden');
                $('.project-nav').on('click', 'a', function (e) {
                    e.preventDefault();
                    var _this = $(this);
                    window.location.hash = _this.attr('href');
                    $('.project-tab').hide();
                    $(_this.attr('href')).show();
                    _this.siblings().addClass('btn-outline');
                    _this.removeClass('btn-outline');
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
                $(d).on('click', '.in-iframe', function (e) {
                    e.preventDefault();
                    var href = this.href;
                    if (href.indexOf('?') < 0) {
                        href += '?iframe=1';
                    } else {
                        href += '&iframe=1';
                    }
                    modalContent.html('<iframe src="' + href + '" width="100%" height="100%" border="0" frameborder="0" style="border: none"></iframe>');
                    iframeModal.find('.modal-title').text($(this).attr('title') ? $(this).attr('title') : $(this).data('title'));
                    iframeModal.modal();
                });
            });

            $('#WBSTreeToggle').on('click', function (e) {
                e.preventDefault();

                $('#wbs-panel-container').toggle();
                $('#wbs-display-container').toggleClass('col-sm-9 col-sm-12');
                $(this).find('i.fa').toggleClass('fa-angle-double-right fa-angle-double-left');
            });

            $('#resourceData').on('click', '.resource-paging-links a', function (e) {
                e.preventDefault();
                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                $.ajax({url: this.href}).success(function (page) {
                    var newHtml = $(page).find('#resourceData').html();
                    $('#resourceData').html(newHtml);
                });
            });

        }(window, document, jQuery));

    </script>


    <script>
        $(document).ready(function () {
            $('form[class=delete_form]').submit(function (e) {
                var type = $(this).data('name');
                var answer = confirm("Are you sure you want to delete " + type + " ?");
                if (!answer) {
                    return false;
                }
            });
        });
    </script>
    <script src="{{asset('/js/project.js')}}"></script>
    {{--    <script src="{{asset('/js/resources.js')}}"></script>--}}
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection