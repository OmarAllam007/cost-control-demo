@extends('layouts.app')

@section('header')
    @include('project.cost-control.header')
@stop

@section('body')
    @can('activity_mapping', $project)
        @if (!App\ActivityMap::forProject($project)->exists())
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                No activity mapping for this project project. Please <a href="/activity-map/import/{{$project->id}}">upload
                    activity mapping here</a>.
            </div>
        @endif
    @endcan

    <div id="projectArea" class="hidden">
        <nav id="project-nav" class="project-nav btn-toolbar pull-right">
            <a href="#datasheet" class="btn btn-primary btn-sm btn-outline"><i class="fa fa-table"></i> Data sheet</a>
            <a href="#Resources" class="btn btn-info btn-sm btn-outline">Resources</a>

            @can('periods', $project)
                <a href="#periods" class="btn btn-sm btn-violet btn-outline"><i class="fa fa-calendar"></i> Financial
                    Periods</a>
            @endcan

            @can('actual_resources', $project)
                <a href="#data-uploads" class="btn btn-sm btn-info btn-outline"><i class="fa fa-upload"></i> Data
                    Uploads</a>
            @endcan

            @can('reports', $project)
                <a href="#CostControlReports" class="btn btn-success btn-sm btn-outline"><i class="fa fa-bar-chart"></i>
                    Reports</a>
            @endcan
        </nav>
        <div class="clearfix"></div>

        @include('project.cost-control.datasheet')

        @include('project.cost-control.resources')

        @can('periods', $project)
            @include('project.cost-control.periods')
        @endcan

        @can('actual_resources', $project)
            @include('project.cost-control.data-uploads')
        @endcan

        @can('reports', $project)
            @include('project.cost-control._report')
        @endcan
    </div>

    @include('project.templates.iframe-modal')

    @can('cost_owner', $project)
        @include('project.cost-control.rollup-modal')
    @endcan
@stop

@section('javascript')
    <script>
        $(function () {
            $('.project-tab').hide();
            $('#projectArea').removeClass('hidden');

            var projectNav = $('#project-nav').on('click', 'a', function (e) {
                e.preventDefault();
                $('.project-tab').hide();
                $($(this).attr('href')).show();
                projectNav.find('a').removeClass('active');
                $(this).addClass('active');
            });

            $('#WBSTreeToggle').on('click', function (e) {
                e.preventDefault();

                $('#wbs-panel-container').toggle();
                $('#wbs-display-container').toggleClass('col-sm-9 col-sm-12');
                $(this).find('i.fa').toggleClass('fa-angle-double-right fa-angle-double-left');
            });

            projectNav.find('a:first').click();

            var iframeModal = $('#IframeModal');
            var modalContent = iframeModal.find('.modal-body');
            $(document).on('click', '.in-iframe', function (e) {
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
        })
    </script>
    <script src="{{asset('/js/tree-select.js')}}"></script>
    <script src="{{asset('/js/cost-control.js')}}"></script>
@endsection