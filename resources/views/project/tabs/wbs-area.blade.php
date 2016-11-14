<section id="wbsArea">
    <div class="row">
        <div class="col-sm-4">
            <aside class="panel panel-default wbs-panel">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title  pull-left">WBS</h3>
                    <div class="btn-toolbar pull-right">
                        <a href="/wbs-level/create?project={{$project->id}}&wbs=@{{selected}}" data-title="Add WBS Level" class="btn btn-sm btn-default in-iframe" title="Add Level"><i class="fa fa-plus"></i></a>
                        <a href="{{route('wbs-level.import', $project)}}" data-title="Import WBS" class="btn btn-sm btn-success in-iframe" title="import"><i class="fa fa-cloud-upload"></i></a>
                        <a href="/wbs-level/@{{selected}}/edit" class="btn btn-sm btn-primary in-iframe" title="Edit WBS Level" v-show="selected"><i class="fa fa-edit"></i></a>
                        @can('wipe')
                            <a href="#WipeWBSModal" data-toggle="modal" class="btn btn-sm btn-danger" title="Delete all"><i class="fa fa-trash"></i></a>
                        @endcan
                    </div>
                </div>


                    @include('project.templates.wbs', compact('wbsTree'))

            </aside>
        </div>


        <div class="col-sm-8">
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
