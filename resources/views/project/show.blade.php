@extends('layouts.app')

@section('header')
    <h2 class="panel-title">Project - {{$project->name}}</h2>

    <form action="{{ route('project.destroy', $project)}}" class="pull-right" method="post">
        @can('modify', $project)
            {{csrf_field()}} {{method_field('delete')}}
            <a href="{{ route('project.edit', $project)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
            <button class="btn btn-sm btn-warning" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
        @endcan

        <a href="{{ route('project.index')}}" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> Back</a>
    </form>
@stop

@section('body')

    <nav class="project-nav">
        @can('budget', $project)
        <a href="#wbsArea" class="btn btn-primary">WBS &amp; Activity</a>
        <a href="#ResourcesArea" class="btn btn-outline btn-primary">Resources</a>
        <a href="#ProductivityArea" class="btn btn-outline btn-primary">Productivity</a>
        <a href="#BreakdownTemplateArea" class="btn btn-outline btn-primary">Breakdown Templates</a>
        @endcan

        @can('reports', $project)
            <a href="#ReportsArea" class="btn btn-outline btn-success">Reports</a>
        @endcan
    </nav>

    <div id="projectArea" class="hidden">
        @can('budget', $project)
            @include('project.tabs.wbs-area')
            @include('project.tabs._resources')
            @include('project.tabs._productivity')
            @include('project.tabs._breakdown_template')
        @endcan

        @can('reports', $project)
            @include('project.tabs._report')
        @endcan
    </div>

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

            $('#resourceData').on('click', '.resource-paging-links a', function(e){
                e.preventDefault();
                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                $.ajax({url: this.href}).success(function(page) {
                    var newHtml = $(page).find('#resourceData').html();
                    $('#resourceData').html(newHtml);
                });
            });

            $('<div class="a"></div>');

        }(window, document, jQuery));

    </script>
    <script src="{{asset('/js/project.js')}}"></script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
@endsection