@php $division_tree =  new \App\Support\ActivityDivisionTree @endphp

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

    <breakdown-templates
            :can_edit="{{can('breakdown_templates',$project)}}"
            :can_delete="{{can('breakdown_templates',$project)}}"
            :divisions="{{$division_tree->get()}}">
    </breakdown-templates>
</section>






