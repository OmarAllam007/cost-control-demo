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

    @can('cost_owner', $project)
        <a href="#RollupModal" class="btn btn-sm btn-info btn-outline" data-toggle="modal">
            <i class="fa fa-compress"></i> Rollup
        </a>
    @endcan

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