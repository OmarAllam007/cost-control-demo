<resources project="{{$project->id}}" inline-template>
    <section id="ResourcesArea">
        <div class="form-group tab-actions pull-right">

            @can('resources', $project)
                <a href="{{route('resources.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Add resource
                </a>

                <div class="btn dropdown" style="padding: 0px">
                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenu1"
                            data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        Importing
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
            @endcan

            <a href="{{route('resources.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>
        </div>
        <div class="clearfix"></div>

        <section class="filters row">

            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::label('resource_name', 'Resource Name', ['class' => 'control-label'])}}
                    {{Form::text('resource_name', null /*session('filters.breakdown.' . $project->id . '.resource_code')*/,
                   ['class' => 'form-control', 'v-model' => 'resource'])}}
                </div>
            </div>

            {{--<div class="col-sm-3">--}}
            {{--<div class="form-group form-group-sm">--}}
            {{--{{Form::label('resource_type', 'Resource Type', ['class' => 'control-label'])}}--}}
            {{--<div class="btn-group btn-group-sm btn-group-block">--}}
            {{--<a href="#ResourceTypeModal2" data-toggle="modal" class="tree-open btn btn-default btn-block">{{session('filters.resources.'.$project->id.'.resource_type')?--}}
            {{--App\ResourceType::with('parent')->find(session('filters.resources.'.$project->id.'.resource_type'))->path : 'Select Resource Type' }}</a>--}}
            {{--<a href="#" @click="resource_type = ''" class="remove-tree-input btn btn-warning" data-target="#ResourceTypeModal2" data-label="Select Resource Type"><span class="fa--}}
            {{--fa-times-circle"></span></a>--}}
            {{--</div>--}}

            {{--</div>--}}
            {{--</div>--}}
        </section>

        <div class="scrollpane" v-if="filtered_resources.length || count">
            <table class="table table-condensed table-striped table-fixed">
                <thead>
                <tr>
                    <th class="col-xs-2">Code</th>
                    <th class="col-xs-3">Resource</th>
                    <th class="col-xs-2">Type</th>
                    <th class="col-xs-1">Rate</th>
                    <th class="col-xs-1">Unit</th>
                    <th class="col-xs-1">Waste</th>
                    <th class="col-xs-2">
                        @can('resources', $project) Actions @endcan
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="resource in filtered_resources">
                    <td class="col-xs-2">@{{resource.resource_code}}</td>
                    <td class="col-xs-3">@{{resource.name}}</td>
                    <td class="col-xs-2">@{{resource.root_type}}</td>
                    <td class="col-xs-1">@{{resource.rate}}</td>
                    <td class="col-xs-1">@{{resource.unit}}</td>
                    <td class="col-xs-1">@{{resource.waste}} </td>
                    <td class="col-xs-2">
                        @can('resources', $project)
                            <a href="/resources/@{{resource.id}}/edit" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                    </td>
                </tr>
                </tbody>
            </table>
            <pagination :total="count"></pagination>
        </div>
        <div v-else class="alert alert-info"><i class="fa fa-info-circle"></i> No resources found</div>


        @include('resource-type._modal', ['input' => 'resource_type', 'value' => ''])

        @can('wipe')
            <div class="modal fade" tabindex="-1" role="dialog" id="WipeResources">
                <form class="modal-dialog" action="{{route('project-resources.wipeAll',$project)}}" method="post">
                    {{csrf_field()}}
                    {{method_field('delete')}}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Delete All Project Resources</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                Are you sure you want to delete all resources ?
                                <input type="hidden" name="wipe" value="1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Delete All</button>
                            <button type="button" class="btn btn-default"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        @endcan
        @include('resource-type._modal2', ['input' => 'resource_type', 'value' => ''])
    </section>
</resources>
