<section id="BreakdownTemplateArea" class="project-tab">
    <div class="form-group tab-actions clearfix">
        <div class="pull-right">
            <a href="{{route('breakdown-template.create', ['project' => $project])}}"
               class="btn btn-primary btn-sm in-iframe" title="Add Template">
                <i class="fa fa-plus"></i> Add Template
            </a>
        </div>
    </div>
    @if ($project->templates->count())
        @foreach($project->templates as $template)
            <ul class="list-unstyled tree">
                <li>
                    <div class="blue-fourth-level">{{$template->name}}</div>
                    <ul class="list-unstyled">
                        <li>
                            <table class="table table-condensed table-striped table-fixed">
                                <thead>
                                <tr>
                                    <th class="col-xs-6">Name</th>
                                    <th class="col-xs-6">Actions</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($template->resources as $resource)
                                    <tr>
                                        <td class="col-xs-6"><a
                                                    href="{{ route('breakdown-template.show', ['template'=>$template]) }}">{{$resource->resource->name}}</a>

                                        </td>
                                        <td class="col-xs-6">
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fa fa-pencil"></i> Override
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                            @endforeach
                            @else
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> No Templates found
                                </div>
                        </li>
                    </ul>

                </li>
            </ul>
            @endif
</section>

