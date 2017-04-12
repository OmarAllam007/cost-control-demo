<resources project="{{$project->id}}" inline-template>
    <section id="Resources" class="project-tab">

        <div class="form-group pull-right tab-actions">
            <a href="{{route('resources-cost.export',$project->id)}}" class="btn btn-info btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>
        </div>
        <div class="clearfix"></div>

        <div class="filters row">
            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::label('resource_name', 'Name', ['class' => 'control-label'])}}
                    {{Form::text('resource_name', null , ['class' => 'form-control', 'v-model' => 'filters.name'])}}
                </div>
            </div>
        </div>

        <div v-if="filtered_resources.length">
            <table class="table table-striped table-condensed table-hover table-fixed">
                <thead>
                <tr>
                    <th class="col-sm-2">Code</th>
                    <th class="col-sm-4">Name</th>
                    <th class="col-sm-2">Type</th>
                    <th class="col-sm-2">Rate</th>
                    <th class="col-sm-2">U.O.M</th>
                    {{--<th class="col-sm-2">Actions</th>--}}
                </tr>
                </thead>

                <tbody>
                <tr v-for="resource in filtered_resources">
                    <td class="col-sm-2">@{{ resource.code }}</td>
                    <td class="col-sm-4">@{{ resource.name }}</td>
                    <td class="col-sm-2">@{{ resource.type }}</td>
                    <td class="col-sm-2">@{{ resource.rate|number_format }}</td>
                    <td class="col-sm-2">@{{ resource.measure_unit }}</td>
                    {{--<td class="col-sm-2">
                        <a href="/cost-control/@{{ project }}/resource/@{{ resource.id }}"
                           class="btn btn-sm btn-primary">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </td>--}}
                </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="alert alert-warning"><i class="fa fa-info-circle"></i> No resources found</div>
    </section>
</resources>