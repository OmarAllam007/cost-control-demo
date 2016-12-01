<section id="ResourcesArea" class="project-tab">
    <div class="form-group tab-actions pull-right">
        {{--<a href="{{ route('resources.create',['project' => $project->id]) }} " class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add resource
        </a>--}}
        <a href="{{route('resources.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
    </div>
    <div class="clearfix"></div>

    @if ($project->plain_resources->count())
        <table class="table table-condensed table-striped table-fixed">
            <thead>
            <tr>
                <th class="col-xs-2">Code</th>
                <th class="col-xs-3">Resource</th>
                <th class="col-xs-2">Type</th>
                <th class="col-xs-2">Rate</th>
                <th class="col-xs-1">Unit</th>
                <th class="col-xs-1">Waste</th>
                <th class="col-xs-1">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($project->plain_resources as $resource)
                <tr>
                    <td class="col-xs-2">{{$resource->resource_code}}</td>
                    <td class="col-xs-3">{{$resource->name}}</td>
                    <td class="col-xs-2">{{$resource->types->root->name or ''}}</td>
                    <td class="col-xs-2">{{number_format($resource->rate, 2)}}</td>
                    <td class="col-xs-1">{{$resource->units->type or ''}}</td>
                    <td class="col-xs-1">{{number_format($resource->waste, 2)}} %</td>
                    <td class="col-xs-1">
                        <a href="{{route('resources.override', ['resources' => $resource->resource_id ?: $resource->id, 'project' => $project])}}"
                           class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No resources found</div>
    @endif
</section>