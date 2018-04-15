@php $division_tree = app(\App\Support\ActivityDivisionTree::class) @endphp

<section id="BreakdownTemplateArea">
    <section class="filters mb-1 clearfix">

        @can('breakdown_templates', $project)
            <div class="tab-actions">
                <div class="pull-right">
                    @can('budget_owner', $project)
                        <div class="dropdown" style="display: inline-block">
                            <a href="#" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                Import/Export
                                <span class="caret"></span>
                            </a>

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

    <breakdown-templates :can_edit="{{can('breakdown_templates',$project)}}"
                         :can_delete="{{can('budget_owner',$project)}}"
                         :divisions="{{$division_tree->get()}}"
                         project_id="{{$project->id}}">
    </breakdown-templates>
</section>
