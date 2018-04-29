<datasheet id="BreakdownTemplate" project="{{$project->id}}" inline-template>
    <div class="breakdown">
        @if ($project->open_period())

            <div class="loader" v-show="loading">
                <i class="fa fa-refresh fa-spin fa-3x"></i>
            </div>

            <section class="form-group btn-toolbar pull-right">
                <div class="btn-group">
                    <button type="button"
                            :class="{'btn btn-sm': true, 'btn-info': perspective != 'budget', 'btn-default': perspective == 'budget'}"
                            @click ="perspective = 'cost'">
                        <i class="fa fa-cube"></i> Current Only
                    </button>
                    <button type="button"
                            :class="{'btn btn-sm': true, 'btn-info': perspective == 'budget', 'btn-default': perspective != 'budget'}"
                            @click="perspective = 'budget'">
                        <i class="fa fa-cubes"></i> All Resources
                    </button>
                </div>

                @can('cost_owner', $project)
                    <div class="dropdown pull-left">
                        <a href="#" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                            &sum; Summarize Resources <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="#" @click.prevent="sumResourcesOnCostAccount">Cost Account Level</a></li>
                            <li><a href="#" @click.prevent="sumResourcesOnActivity">Activity Level</a></li>
                        </ul>
                    </div>
                    <a href="#DeleteWbsDataModal" data-toggle="modal" class="btn btn-danger btn-sm" type="button">
                        <i class="fa fa-remove"></i> Delete current
                    </a>
                @endcan


            </section>
            <div class="clearfix"></div>

            <section class="filters row" id="breakdown-filters">
                @include('std-activity._modal', ['input' => 'activity', 'value' => ''])
                @include('resource-type._modal', ['input' => 'resource_type', 'value' => ''])

                <div class="col-sm-3">
                    <div class="form-group form-group-sm">
                        <div class="btn-group btn-group-sm btn-group-block">
                            <a href="#ActivitiesModal" data-toggle="modal"
                               class="btn btn-default btn-block tree-open">{{ session('filters.breakdown.'.$project->id.'.activity')? App\StdActivity::find(session('filters.breakdown.'.$project->id.'.activity'))->name : 'Select Activity' }}</a>
                            <a href="#" @click="activity = ''" class="remove-tree-input btn btn-warning" data-target="
                        #ActivitiesModal" data-label="Select Activity"><span class="fa fa-times-circle"></span></a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group form-group-sm">
                        {{Form::text('cost_account', session('filters.breakdown.' . $project->id . '.cost_account'), ['class' => 'form-control', 'v-model' => 'cost_account', 'placeholder' => 'Cost Account' , 'debounce' => 500])}}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group form-group-sm">
                        <div class="btn-group btn-group-sm btn-group-block">
                            <a href="#ResourceTypeModal" data-toggle="modal"
                               class="tree-open btn btn-default btn-block">Select Resource Type</a>

                            <a href="#" @click="resource_type = ''"
                                class="remove-tree-input btn btn-warning"
                                data-target="#ResourceTypeModal" data-label="Select Resource Type">
                                <span class="fa fa-times-circle"></span>
                            </a>
                        </div>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group form-group-sm">
                        {{Form::text('resource', session('filters.breakdown.'.$project->id.'.resource'), ['class' => 'form-control', 'v-model' => 'resource', 'placeholder' => 'Resource Code or Name'])}}
                    </div>
                </div>
            </section>

            <section v-if="show_breakdowns">
                <div class="vertical-scroll-pane">
                    <section class="activity-section" v-for="activity in breakdowns">
                        <header class="display-flex breakdown-activity-header">
                            <h5 class="flex"><a data-toggle="collapse" :href="'#' + slug(activity.code)" v-text="activity.activity + ' (' + activity.code + ')'"></a></h5>

                            <div class="btn-toolbar">
                                <button class="btn btn-default btn-sm" @click="doRollup(activity.code)">
                                    <i class="fa fa-compress"></i> Rollup
                                </button>

                                <a class="btn btn-info btn-sm" :href="'/activity-log/'+ activity.wbs_id + '/' + activity.code" target="_blank">
                                    <i class="fa fa-history"></i> Activity Log
                                </a>
                            </div>
                        </header>

                        <div :id="slug(activity.code)" class="collapse">
                            @include('project.cost-control._breakdown_resource')
                        </div>
                    </section>

                </div>
            </section>

            <div class="alert alert-info" v-else><i class="fa fa-info-circle"></i> No breakdowns found</div>

            <div class="text-center mt-20">
                <pagination :url="url" property="breakdowns"></pagination>
            </div>

            @can('delete_resources', $project)
                <delete-resource-modal inline-template>
                    <form class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">&times;
                                    </button>
                                    <h4 class="modal-title">Delete resource data</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="lead">
                                        Are you sure you want to delete data for this resource for
                                        period {{$project->open_period()->name}}?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" @click="delete_resource" class="btn btn-danger" :disabled="
                                    loading"><i :class="'fa ' + (loading? 'fa-spinner fa-spin' : 'fa-trash')"></i>
                                    Delete</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </delete-resource-modal>

                <delete-activity-modal inline-template>
                    <form class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">&times;
                                    </button>
                                    <h4 class="modal-title">Delete activity data</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="lead">
                                        Are you sure you want to delete data for this activity for
                                        period {{$project->open_period()->name}}?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" @click="delete_activity" class="btn btn-danger" :disabled="
                                    loading"><i :class="'fa ' + (loading? 'fa-spinner fa-spin' : 'fa-trash')"></i>
                                    Delete</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </delete-activity-modal>
            @endcan
        @else
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                No open period in the project.
                @can('periods', $project)
                    Please <a href="/period/create?project={{$project->id}}">add a period here</a>.
                @endcan
            </div>
        @endif

        @can('cost_owner', $project)
            <div class="modal fade" tabindex="-1" role="dialog" id="DeleteWbsDataModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            <h4 class="modal-title">Delete current data</h4>
                        </div>
                        <div class="modal-body">
                            <p class="lead alert alert-danger">
                                Are you sure you want to delete all current data for this WBS and all its children?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" :disabled="loading" @click="deleteWbsCurrent">
                            <i :class="{'fa': true, 'fa-remove': !loading, 'fa-spinner fa-spin': loading}"></i>
                            Yes Delete
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</datasheet>

