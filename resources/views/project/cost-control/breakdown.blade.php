<datasheet id="BreakdownTemplate" project="{{$project->id}}" inline-template>
    <div class="breakdown">
        @if ($project->open_period())

            <div class="loader" v-show="loading">
                <i class="fa fa-refresh fa-spin fa-3x"></i>
            </div>

            <section class="form-group btn-toolbar pull-right">
                @can('actual_resources', $project)
                    <a :href="rollup_url" type="button" class="btn btn-primary btn-sm in-iframe" title="Rollup Resources" v-show="rollup.length > 1" @click="doRollup">
                        <i class="fa fa-compress"></i> Rollup
                    </a>
                @endcan

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
                        {{Form::label('activity', 'Activity', ['class' => 'control-label'])}}
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
                        {{Form::label('cost_account', 'Cost Account', ['class' => 'control-label'])}}
                        {{Form::text('cost_account', session('filters.breakdown.' . $project->id . '.cost_account'), ['class' => 'form-control', 'v-model' => 'cost_account', 'debounce' => 500])}}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group form-group-sm">
                        {{Form::label('resource_type', 'Resource Type', ['class' => 'control-label'])}}
                        <div class="btn-group btn-group-sm btn-group-block">
                            <a href="#ResourceTypeModal" data-toggle="modal"
                               class="tree-open btn btn-default btn-block">{{session('filters.breakdown.'.$project->id.'.resource_type')? App\ResourceType::with('parent')->find(session('filters.breakdown.'.$project->id.'.resource_type'))->path : 'Select Resource Type' }}</a>
                            <a href="#" @click="resource_type = ''" class="remove-tree-input btn btn-warning"
                            data-target="
                        #ResourceTypeModal" data-label="Select Resource Type"><span
                                    class="fa fa-times-circle"></span></a>
                        </div>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group form-group-sm">
                        {{Form::label('resource', 'Resource Name', ['class' => 'control-label'])}}
                        {{Form::text('resource', session('filters.breakdown.'.$project->id.'.resource'), ['class' => 'form-control', 'v-model' => 'resource'])}}
                    </div>
                </div>
            </section>

            <section v-if="show_breakdowns">
                <div class="vertical-scroll-pane">
                    <section class="activity-section" v-for="(activity, resources) in breakdowns">

                        <header class="display-flex breakdown-activity-header">
                            <h5 class="flex">@{{ activity }}</h5>

                            <button class="btn btn-default btn-sm"><i class="fa fa-compress"></i> Rollup</button>
                        </header>

                        <breakdown-resource inline-template
                                            v-for="resource in resources"
                                            :resource="resource"
                                            :rollup_activity="rollup_activity"
                                            :rollup_wbs="rollup_wbs">

                            <article class="breakdown-resource display-flex ">
                                <section class="information flex">
                                    <div class="basic-info flex">
                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Code</span> @{{resource.code}}</span>
                                            <span class="flex"><span class="tag">Activity</span> @{{resource.activity}}</span>
                                            <span class="flex"><span class="tag">Cost Account</span> @{{resource.cost_account}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Resource Type</span> @{{resource.resource_type}}</span>
                                            <span class="flex"><span class="tag">Resource Code</span> @{{resource.resource_code}}</span>
                                            <span class="flex"><span class="tag">Resource Name</span> @{{resource.resource_name}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Budget Unit</span> @{{resource.budget_unit|number_format}}</span>
                                            <span class="flex"><span class="tag">Unit Price</span> @{{resource.unit_price|number_format}}</span>
                                            <span class="flex"><span class="tag">Budget Cost</span> @{{resource.budget_cost|number_format}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Budget Qty</span> @{{resource.resource_qty|number_format}}</span>
                                            <span class="flex"><span class="tag">Eng Qty</span> @{{resource.resource_qty|number_format}}</span>
                                            <span class="flex"><span class="tag">Resource Qty</span> @{{resource.resource_qty|number_format}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">To Date Qty</span> @{{resource.to_date_qty|number_format}}</span>
                                            <span class="flex"><span class="tag">To Date Unit Price</span> @{{resource.to_date_unit_price|number_format}}</span>
                                            <span class="flex"><span class="tag">To Date Cost</span> @{{resource.to_date_cost|number_format}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Allowable Cost</span> @{{resource.allowable_ev_cost|number_format}}</span>
                                            <span class="flex"><span class="tag">Status</span> @{{resource.status || "Not Started" }}</span>
                                            <span class="flex"><span class="tag">Progress</span> @{{resource.progress|number_format}}%</span>
                                        </div>
                                    </div>
                                    <section class="extended" v-show="expanded">
                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Current Qty</span> @{{resource.current_qty|number_format}}</span>
                                            <span class="flex"><span class="tag">Current Unit Price</span> @{{resource.current_unit_price|number_format}}</span>
                                            <span class="flex"><span class="tag">Current Cost</span> @{{resource.current_cost|number_format}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">Remaining Qty</span> @{{resource.remaining_qty|number_format}}</span>
                                            <span class="flex"><span class="tag">Remaining Unit Price</span> @{{resource.remaining_unit_price|number_format}}</span>
                                            <span class="flex"><span class="tag">Remaining Cost</span> @{{resource.remaining_cost|number_format}}</span>
                                        </div>

                                        <div class="display-flex">
                                            <span class="flex"><span class="tag">At Completion Qty</span> @{{resource.at_completion_qty|number_format}}</span>
                                            <span class="flex"><span class="tag">At Completion Unit Price</span> @{{resource.at_completion_unit_price|number_format}}</span>
                                            <span class="flex"><span class="tag">At Completion Cost</span> @{{resource.at_completion_cost|number_format}}</span>
                                        </div>
                                    </section>
                                </section>

                                <section class="actions">
                                    <button type="button"
                                            @click="add_to_rollup" class="btn btn-xs"
                                            :class="is_rolled_up? 'btn-success' : 'btn-info'"
                                            title="Add to rollup" :disabled="!can_be_rolled_up">
                                        <i :class="['fa fa-fw', is_rolled_up? 'fa-check' : 'fa-plus']"></i>
                                    </button>

                                    <div class="dropdown">
                                        <a href="#" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="Menu">
                                            <i class="fa fa-bars"></i>
                                        </a>

                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('manual_edit', $project)
                                                <li><a class="in-iframe" :href="'/cost/' + resource.breakdown_resource_id + '/pseudo-edit'" title="Edit Resource Data"><i class="fa fa-fw fa-edit"></i> Edit Resource</a></li>
                                            @endcan
                                            @can('delete_resources', $project)
                                                <li><a href="#" @click.prevent="deleteResource(resource)" title="Delete resource data"><i class="fa fa-fw fa-trash"></i> Delete Resource Data</a></li>
                                                <li><a href="#" @click.prevent="deleteActivity(resource)" title="Delete activity data"><span class="text-danger"><i class="fa fa-fw fa-remove"></i> Delete Activity Data</span></a></li>
                                            @endcan
                                        </ul>
                                    </div>

                                    <button title="More Information" class="btn btn-sm btn-xs btn-default" @click="expanded = !expanded">
                                        <i :class="{'fa fa-fw': true, 'fa-angle-down': !expanded, 'fa-angle-up': expanded}"></i>
                                    </button>
                                </section>

                            </article>
                        </breakdown-resource>
                    </section>

                </div>
            </section>

            <div class="alert alert-info" v-else><i class="fa fa-info-circle"></i> No breakdowns found</div>

            <pagination :url="url"></pagination>

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

