@extends('layouts.app')

@section('header')
    <h2>{{ $project->name }}</h2>

    <nav class="btn-toolbar pull-right">
        <div class="btn-group">
            <button class="btn btn-outline btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-cloud-download"></i> Export <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="{{route('costshadow.export',$project)}}"><i class="fa fa-cube"></i> Current Period</a></li>
                <li><a href="{{route('costshadow.export',$project)}}?perspective=budget"><i class="fa fa-cubes"></i> All Resources</a></li>
            </ul>
        </div>

        @if (can('activity_mapping', $project) || can('resource_mapping', $project) || ($project->is_cost_ready && can('actual_resources', $project)))
            <div class="btn-group">
                <a href="#import-links" class="btn btn-outline btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-cloud-upload"></i> Import <span class="caret"></span>
                </a>
                <ul id="import-link" class="dropdown-menu">
                    @if ($project->is_cost_ready)
                        @can('actual_resources', $project)
                            <li><a href="{{route('actual-material.import', $project)}}">Resources</a></li>
                        @endcan
                    @endif

                    @can('activity_mapping', $project)
                        <li><a href="{{route('activity-map.import', $project)}}">Activity Mapping</a></li>
                    @endcan

                    @can('resource_mapping', $project)
                        <li><a href="{{route('resources.import-codes', compact('project'))}}">Resource Mapping</a></li>
                    @endcan

                    @can('cost_owner', $project)
                        @if ($project->periods()->count() == 1)
                            <li><a href="{{route('cost.old-data', $project)}}">Import Old Data</a></li>
                        @endif
                    @endcan

                    {{--<li><a href="{{route('actual-revenue.import', $project)}}">Actual Revenue</a></li>--}}
                </ul>
            </div>
        @endif


        <a href="{{ route('project.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-chevron-left"></i> Back
        </a>
    </nav>
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