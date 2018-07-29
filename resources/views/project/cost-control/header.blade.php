<h2>
    {{ $project->name }}

    @if ($project->is_activity_rollup)
        <small class="label label-default">Activity</small>
    @elseif ($project->hasRollup())
        <small class="label label-default">Semi Activity</small>
    @endif
</h2>

<nav class="btn-toolbar pull-right">
    @can('actual_resources', $project)
        <a href="#RollupModal" class="btn btn-sm btn-info btn-outline" data-toggle="modal">
            <i class="fa fa-compress"></i> Rollup
        </a>
    @endcan

    <div class="btn-group">
        <button class="btn btn-outline btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-cloud-download"></i> Export <span class="caret"></span>
        </button>

        <ul class="dropdown-menu">
            <li><a href="{{route('costshadow.export',$project)}}"><i class="fa fa-fw fa-cube"></i> Current Period</a></li>
            <li><a href="{{route('costshadow.export',$project)}}?perspective=budget"><i class="fa fa-fw fa-cubes"></i> All Resources</a></li>
            <li><a href="{{route('break_down.export',$project)}}"><i class="fa fa-fw fa-bars"></i> Breakdown</a></li>
            <li><a href="{{route('project.export-progress',$project)}}"><i class="fa fa-fw fa-arrow-circle-o-right"></i> Progress</a></li>
            <li><a href="{{route('activity_mapping.export',$project)}}"><i class="fa fa-fw fa-cloud-download"></i> Activity Mapping</a></li>
            <li><a href="{{route('resource_mapping.export',$project)}}"><i class="fa fa-fw fa-cloud-download"></i> Resource Mapping</a></li>
        </ul>
    </div>

    @if (can('activity_mapping', $project) || can('resource_mapping', $project) || ($project->is_cost_ready && can('actual_resources', $project)))
        <div class="btn-group">
            <a href="#import-links" class="btn btn-outline btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-cloud-upload"></i> Import <span class="caret"></span>
            </a>
            <ul id="import-link" class="dropdown-menu dropdown-menu-right">
                @if ($project->is_cost_ready)
                    @can('actual_resources', $project)
                        <li><a href="{{route('actual-material.import', $project)}}">Actual Resources</a></li>
                        <li><a href="{{route('project.update-progress', $project)}}">Update Progress</a></li>
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


    <a href="{{ route('home.cost-control') }}" class="btn btn-default btn-sm">
        <i class="fa fa-chevron-left"></i> Back
    </a>
</nav>