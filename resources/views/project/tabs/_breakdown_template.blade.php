<section id="BreakdownTemplateArea" class="project-tab">
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
            @foreach($project->templates->sortBy('name') as $breakdown_template)
                <tr>
                    <td class="col-xs-8"><a
                                href="/breakdown-template/{{$breakdown_template->id}}?project_id={{$project->id}}">{{ $breakdown_template->name }}</a>
                    </td>

                    <td class="col-xs-4">
                        @can('breakdown_templates',$project)
                            <form action="{{ route('breakdown-template.destroy', $breakdown_template->id) }}" method="post">
                                {{csrf_field()}} {{method_field('delete')}}
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        @endcan

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</section>

