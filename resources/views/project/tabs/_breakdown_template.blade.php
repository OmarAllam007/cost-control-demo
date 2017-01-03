<section id="BreakdownTemplateArea" class="project-tab">
    <div class="form-group tab-actions clearfix">
        @can('breakdown_templates',$project)
        <div class="pull-right">
            <a href="{{route('breakdown-template.create', ['project' => $project])}}"
               class="btn btn-primary btn-sm " title="Add Template">
                <i class="fa fa-plus"></i> Add Breakdown Template
            </a>

            <a href="{{route('breakdown-template.create', ['project' => $project,'import'=>true])}}"
               class="btn btn-success btn-sm " title="Import Template">
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
                            <form action="{{ route('breakdown-template.destroy', $breakdown_template->id) }}" method="post" class="delete_form" data-name="Template">
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
    <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete ?</div>
                <input type="hidden" name="wipe" value="1">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger delete"><i class="fa fa-fw fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>
</section>






