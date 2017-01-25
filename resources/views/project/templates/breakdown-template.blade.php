<breakdown-template project="{{$project->id}}" inline-template>
    <section id="BreakdownTemplateArea">
        <div class="form-group tab-actions clearfix">
            @can('breakdown_templates',$project)
                <div class="pull-right">
                    <a href="{{route('breakdown-template.create', ['project' => $project])}}"
                       class="btn btn-primary btn-sm in-iframe" title="Add Template">
                        <i class="fa fa-plus"></i> Add Breakdown Template
                    </a>

                    <a href="{{route('breakdown-template.create', ['project' => $project,'import'=>true])}}"
                       class="btn btn-success btn-sm in-iframe" title="Import Template">
                        <i class="fa fa-level-down" aria-hidden="true"></i>
                        Import Template
                    </a>


                </div>
            @endcan
        </div>

        <section class="filters row">
            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::label('template_name', 'Template Name', ['class' => 'control-label'])}}
                    {{Form::text('template_name', null /*session('filters.breakdown.' . $project->id . '.resource_code')*/,
                   ['class' => 'form-control', 'v-model' => 'template'])}}
                </div>
            </div>
        </section>

        @if ($project->templates->count())
            <table class="table table-condensed table-striped table-fixed">
                <thead>
                <tr>
                    <th class="col-xs-8">Name</th>

                    <th class="col-xs-4"> @can('breakdown_templates',$project)
                            Actions @endcan </th>

                </tr>
                </thead>
                <tbody>
                {{--                @foreach($project->templates->sortBy('name') as $breakdown_template)--}}
                <tr v-for="template in filterd_templates">
                    <td class="col-xs-8"><a
                                :href="'/breakdown-template/' + template.id + '?project_id=' + template.project_id">@{{ template.name }}</a>
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
        @endif
    </section>

</breakdown-template>
{{--@include('std-activity._modal2', ['input' => 'activity', 'value' => session('filters.breakdown.'.$project->id.'.activity')])--}}
