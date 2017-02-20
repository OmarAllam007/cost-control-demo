<section id="datasheet" class="project-tab">
    <div class="row">
        <div class="col-sm-12">
            <a href="#" id="WBSTreeToggle" class="btn btn-default btn-sm"><i class="fa fa-angle-double-left"></i></a>
        </div>
        <div class="col-sm-3" id="wbs-panel-container">
            <aside class="panel panel-default wbs-panel">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title  pull-left">WBS</h3>

                </div>

                @include('project.cost-control.wbs', compact('wbsTree'))

            </aside>
        </div>


        <div class="col-sm-9" id="wbs-display-container">
            <section id="wbs-display" v-show="selected">
                <alert></alert>

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#Breakdown" data-toggle="tab">Resources</a></li>
                    <li><a href="#ActivityLog" data-toggle="tab">Activity Log</a></li>
                </ul>

                <div class="tab-content">
                <article class="tab-pane active" id="Breakdown">
                    @include('project.cost-control.breakdown')
                </article>

                <article class="tab-pane" id="ActivityLog">
                    @include('project.cost-control.activity-log')
                </article>
                </div>
            </section>

            <div class="alert alert-info" v-else>
                <i class="fa fa-info-circle"></i> Please select a WBS
            </div>
        </div>

    </div>
</section>

