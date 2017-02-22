<section id="wbsArea" class="project-tab">
    <div class="row">
        <div class="col-sm-12">
            <a href="#" id="WBSTreeToggle" class="btn btn-default btn-sm"><i class="fa fa-angle-double-left"></i></a>
        </div>
        <div class="col-sm-3" id="wbs-panel-container">
            <aside class="panel panel-default wbs-panel">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title  pull-left">WBS</h3>
                    <div class="btn-group pull-right">
                        <button type="button" data-toggle="dropdown" class="btn btn-sm btn-default dropdown-toggle"><i class="fa fa-bars"></i></button>

                        <ul class="dropdown-menu">
                        @can('wbs', $project)
                            <li><a :href="'/wbs-level/create?project={{$project->id}}&wbs='+selected" data-title="Add WBS Level" class="in-iframe" title="Add Level"><i class="fa fa-fw fa-plus"></i> Add WBS</a></li>
                                <li><a :href="'/wbs-level/' + selected + '/edit'" class="in-iframe" title="Edit WBS Level" v-show="selected"><i class="fa fa-fw fa-edit"></i> Edit WBS Level</a></li>
                                <li><a href="#DeleteWBSModal" data-toggle="modal" title="Delete WBS Level" v-show="selected"><span class="text-danger"><i class="fa fa-fw fa-remove"></i> Delete WBS Level</span></a></li>
                                <li class="divider"></li>
                                <li><a href="{{route('wbs-level.import', $project)}}" data-title="Import WBS" class="in-iframe" title="import"><i class="fa fa-fw fa-cloud-upload"></i> Import WBS Tree</a></li>
                                <li><a href="{{route('wbs-level.export', $project)}}" data-title="Export WBS" title="export"><i class="fa fa-fw fa-cloud-download"></i> Export WBS Tree</a></li>
                        @endcan

                        @can('wipe')
                            <li><a href="#WipeWBSModal" data-toggle="modal" title="Delete all WBS-Levels"><span class="text-danger"><i class="fa fa-fw fa-trash"></i> Wipe WBS Tree</span></a></li>
                        @endcan
                        </ul>
                    </div>
                </div>

                @include('project.templates.wbs', compact('wbsTree'))

                @can('wbs', $project)
                    <div class="modal fade" tabindex="-1" role="dialog" id="DeleteWBSModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    <h4 class="modal-title">Delete WBS Level</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="lead">Are you sure you want to delete this WBS with all its data and children?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" :disabled="loading" @click="deleteWbs"><i :class="{fa: true, 'fa-trash': !loading, 'fa-spinner fa-spin': loading}"></i> Yes Delete</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

            </aside>
        </div>


        <div class="col-sm-9" id="wbs-display-container">
            <section id="wbs-display" v-show="selected">
                <alert></alert>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#Breakdown">Resources</a></li>
                    <li><a href="#BOQ">BOQ</a></li>
                    <li><a href="#QtySurvey">Quantity Survey</a></li>
                </ul>

                <div class="tab-content">
                    <article class="tab-pane active" id="Breakdown">
                        @include('project.templates.breakdown')
                    </article>

                    <article class="tab-pane" id="BOQ">
                        @include('project.templates.boq')
                    </article>

                    <article class="tab-pane" id="QtySurvey">
                        @include('project.templates.qty-survey')
                    </article>
                </div>
            </section>

            <div class="alert alert-info" v-else>
                <i class="fa fa-info-circle"></i> Please select a WBS
            </div>
        </div>

    </div>
</section>

