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

            <section v-if="breakdowns.length">
                <div class="scrollpane">
                    <table class="table table-condensed table-striped table-hover table-breakdown">
                        <thead>
                        <tr>
                            @can('manual_edit', $project)
                                <th style="min-width: 30px; max-width: 30px">&nbsp;</th>
                            @endcan
                            <th style="min-width: 300px; max-width: 300px;" class="bg-blue">Activity</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-black">Breakdown Template</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-blue">Cost Account</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Eng. Qty.</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Budget Qty.</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-blue">Resource Qty.</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-info">Resource Waste</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Resource Type</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Resource Code</th>
                            <th style="min-width: 200px; max-width: 200px;" class="bg-green">Resource Name</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-info">Price/Unit</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-info">Unit of measure</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Budget Unit</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Budget Cost</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-black">BOQ Equivalent Unit Rate
                            </th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-blue">No. Of Labors</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-info">Productivity (Unit/Day)</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-blue">Productivity Ref</th>
                            <th style="min-width: 150px; max-width: 150px;" class="bg-green">Remarks</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Progress</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Status</th>
                            <th class="bg-green" style="min-width: 150px; max-width: 150px;">Prev. Price/Unit</th>
                            <th class="bg-green" style="min-width: 150px; max-width: 150px;">Prev. Qty</th>
                            <th class="bg-green" style="min-width: 150px; max-width: 150px;">Prev. Cost</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Curr. Price/Unit</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Curr. Qty</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Curr. Cost</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">To Date Price / Unit (
                                Eqv.
                                )
                            </th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">To Date. Qty</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">To Date. Cost</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Allawable (EV) cost</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Var +/-</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Remaining Price/Unit</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Remaining Qty</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Remaining Cost</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">BL Allowable Cost</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Var +/- 10</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Completion Price/Unit</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Completion Qty</th>
                            <th class="bg-violet" style="min-width: 150px; max-width: 150px;">Completion Cost</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Price/Unit Var,</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Qty Var +/-</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Var +/-</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Physical Unit</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">(P/W) Index</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance To Date Due
                                to
                                Unit
                                Price
                            </th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Allowable Quantity</th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance Remaining
                                Due to
                                Unit Price
                            </th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance Completion
                                Due
                                to
                                Unit Price
                            </th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance Completion
                                Due
                                to
                                Qty
                            </th>
                            <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance to Date Due
                                to
                                Qty
                            </th>
                        </tr>
                        </thead>
                    </table>
                    <table class="table table-condensed table-striped table-hover table-breakdown">
                        <tbody>
                        <tr v-for="breakdown in breakdowns">
                            @if (can('manual_edit', $project) || can('delete_resources', $project))
                                <td style="min-width: 30px; max-width: 30px">
                                    <div class="dropdown">
                                        <button class="btn btn-xs btn-default" type="button"
                                                data-toggle="dropdown"><span
                                                    class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            @can('manual_edit', $project)
                                                <li><a :href="'/cost/' + breakdown.cost_id + '/edit/'" class="in-iframe"
                                                       title="Edit Resource Data"><i class="fa fa-fw fa-edit"></i> Edit</a>
                                                </li>
                                            @endcan
                                            @can('delete_resources', $project)
                                            <li><a href="#" @click.prevent="deleteResource(breakdown)"><i
                                                            class="fa fa-fw fa-trash"></i> Delete resource data</a></li>
                                            <li><a href="#" @click.prevent="deleteActivity(breakdown)"><span
                                                            class="text-danger"><i class="fa fa-fw fa-remove"></i> Delete activity data</span></a>
                                            </li>
                                            @endcan </ul>
                                    </div>
                                </td>
                            @endif
                            <td style="min-width: 300px; max-width: 300px;"
                                class="bg-blue">@{{ breakdown.activity }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-black">@{{ breakdown.template }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-blue">@{{ breakdown.cost_account }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{breakdown.eng_qty }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{ breakdown.budget_qty }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-blue">@{{ breakdown.resource_qty }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-info">@{{ breakdown.resource_waste }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{ breakdown.resource_type }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{ breakdown.resource_code }}</td>
                            <td style="min-width: 200px; max-width: 200px;"
                                class="bg-green">@{{ breakdown.resource_name }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-info">@{{ breakdown.unit_price }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-info">@{{ breakdown.measure_unit }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{ breakdown.budget_unit }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{ breakdown.budget_cost }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-black">@{{ breakdown.boq_equivilant_rate }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-blue">@{{ breakdown.labors_count }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-info">@{{ breakdown.productivity_output }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-blue">@{{ breakdown.productivity_ref }}</td>
                            <td style="min-width: 150px; max-width: 150px;"
                                class="bg-green">@{{ breakdown.remarks }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown.progress || 0 | number_format }}
                                %
                            </td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown.status || 'Not Started'}}</td>

                            <td class="bg-green"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.prev_unit_price: 0 |number_format }}</td>
                            <td class="bg-green"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.prev_qty: 0 |number_format }}</td>
                            <td class="bg-green"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.prev_cost: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.curr_unit_price: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.curr_qty: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.curr_cost: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.to_date_unit_price: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.to_date_qty: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.to_date_cost: 0 |number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.allowable_ev_cost : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.allowable_var : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.remaining_unit_price : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.remaining_qty : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.remaining_cost : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.bl_allowable_cost : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.bl_allowable_var : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.completion_unit_price : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.completion_qty : 0 | number_format }}</td>
                            <td class="bg-violet"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.completion_cost : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.unit_price_var : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.qty_var : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.cost_var : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.physical_unit : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.pw_index : 0 | number_format }}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_to_date_due_unit_price : 0 | number_format}}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.allowable_qty : 0 | number_format}}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_remaining_due_unit_price : 0 | number_format}}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_completion_due_unit_price : 0 | number_format}}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_completion_due_qty : 0 | number_format}}</td>
                            <td class="bg-orange"
                                style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_to_date_due_qty : 0 | number_format}}</td>
                        </tr>
                        </tbody>
                    </table>

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

