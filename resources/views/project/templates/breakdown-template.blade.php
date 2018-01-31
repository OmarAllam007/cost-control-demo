<breakdown-template project="{{$project->id}}" inline-template>
    <section id="BreakdownTemplateArea">
        <section class="filters row">
            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::text('template_name', null /*session('filters.breakdown.' . $project->id . '.resource_code')*/,
                        ['class' => 'form-control', 'v-model' => 'template', 'placeholder' => 'Search by code or name'])}}
                </div>
            </div>

            @can('breakdown_templates', $project)
                <div class="col-sm-9 form-group tab-actions clearfix">
                    <div class="pull-right">
                        @can('owner', $project)
                            <a href="{{route('breakdown-template.create', ['project' => $project])}}"
                               class="btn btn-primary btn-sm in-iframe" title="Add Template">
                                <i class="fa fa-plus"></i> Add Breakdown Template
                            </a>

                            <div class="dropdown" style="display: inline-block">
                                <a href="#" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">Import/Export <span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{route('breakdown-template.export')}}?project={{$project->id}}">
                                            <i class="fa fa-cloud-download"></i> Export
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{route('breakdown-template.modify')}}?project={{$project->id}}">
                                            <i class="fa fa-cloud-upload"></i> Import
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan

                        <a href="{{route('breakdown-template.create', ['project' => $project,'import'=>true])}}"
                           class="btn btn-success btn-sm in-iframe" title="Import Template">
                            <i class="fa fa-level-down" aria-hidden="true"></i>
                            Import Template
                        </a>
                    </div>
                </div>
            @endcan
        </section>


            <table v-if="count" class="table table-condensed table-striped table-fixed" v-show="filterd_templates.length">
                <thead>
                <tr>
                    <th class="col-xs-2">Code</th>
                    <th class="col-xs-6">Name</th>
                    <th class="col-xs-4"> @can('breakdown_templates',$project) Actions @endcan </th>
                </tr>
                </thead>
                <tbody>
                {{--                @foreach($project->templates->sortBy('name') as $breakdown_template)--}}
                <tr v-for="template in filterd_templates">
                    <td class="col-xs-2">
                        <a :href="'/breakdown-template/' + template.id + '?project_id=' + template.project_id">@{{ template.code }}</a>
                    </td>

                    <td class="col-xs-6">
                        <a :href="'/breakdown-template/' + template.id + '?project_id=' + template.project_id">@{{ template.name }}</a>
                    </td>

                    <td class="col-xs-4">
                        @can('breakdown_templates',$project)
                            <form action="/breakdown-template/@{{ template.id }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a :href="'/breakdown-template/' + template.id + '?project_id=' + template.project_id" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
                {{--@endforeach--}}
                </tbody>
            </table>

            <div v-else class="alert alert-info" v-show="!filterd_templates.length">
                <i class="fa fa-info-circle"></i> No templates found
            </div>

            <pagination :total="templates.length" :per-page="100"></pagination>

    </section>

</breakdown-template>
{{--@include('std-activity._modal2', ['input' => 'activity', 'value' => session('filters.breakdown.'.$project->id.'.activity')])--}}
