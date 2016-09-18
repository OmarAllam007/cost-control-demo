<div class="form-group tab-actions pull-right">
    <a href="{{ route('resources.create',['project' => $project->id]) }} " class="btn btn-sm btn-primary">
    <i class="fa fa-plus"></i> Add resource
</a>
</div>
<div class="clearfix"></div>

@if ($project->plain_resources->count())
    <table class="table table-condensed table-striped">
        <thead>
        <tr>
            <th>Code</th>
            <th>Resource</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($project->plain_resources as $resource)
        <tr>
            <td>{{$resource->resource_code}}</td>
            <td>{{$resource->name}}</td>
            <td>{{$resource->types->root->name or ''}}</td>
            <td>
                <a href="{{route('resources.override', ['resources' => $resource, 'project' => $project])}}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No resources found</div>
@endif