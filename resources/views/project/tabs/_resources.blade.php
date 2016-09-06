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
            <td>{{$resource->types->name}}</td>
            <td>
                <a href="{{route('resources.override', $project)}}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Override</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No resources found</div>
@endif