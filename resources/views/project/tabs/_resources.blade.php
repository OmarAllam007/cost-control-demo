<section id="ResourcesArea" class="project-tab">
    <div class="form-group tab-actions pull-right">

        <a href="{{route('resources.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add resource
        </a>

        <div class="btn dropdown" style="padding: 0px">
            <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                Updating
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li>
                    <a href="{{ route('resources.import',['project'=>$project->id]) }} " class="btn">
                        <i class="fa fa-cloud-upload"></i> Import
                    </a>
                </li>
                <li>
                    <a href="{{route('all-resources.modify',['project'=>$project->id])}}" class="btn">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        Modify
                    </a>
                </li>
            </ul>
        </div>

        <a href="{{route('resources.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
            <i class="fa fa-cloud-download"></i> Export
        </a>
    </div>
    <div class="clearfix"></div>

    <section id="resourceData">
    @if ($project->resources()->count())
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
            @foreach ($projectResources = $project->resources()->paginate(200) as $resource)
                <tr>
                    <td class="col-xs-2">{{$resource->resource_code}}</td>
                    <td class="col-xs-3">{{$resource->name}}</td>
                    <td class="col-xs-2">{{$resource->types->root->name or ''}}</td>
                    <td class="col-xs-2">{{number_format($resource->rate, 2)}}</td>
                    <td class="col-xs-1">{{$resource->units->type or ''}}</td>
                    <td class="col-xs-1">{{number_format($resource->waste, 2)}} %</td>
                    <td class="col-xs-1">
                        <a href="{{route('resources.edit',['resource'=>$resource->id,'project_id'=>$project->id])}}" class="btn btn-primary btn-sm">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="text-center resource-paging-links">
            {{$projectResources->links()}}
        </div>
    @else
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No resources found</div>
    @endif
    </section>
</section>