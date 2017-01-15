<template id="BreakdownTemplate">
    <section class="breakdown">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <div class="form-group tab-actions pull-right">
            <a style="margin-left: 2px;" href="{{route('break_down.export', ['project' => $project->id])}}"
               class="btn btn-info btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>
            @can('breakdown', $project)
                <a href="/breakdown/create?project={{$project->id}}&&wbs_id=@{{ wbs_id }}"
                   class="btn btn-primary btn-sm in-iframe" id="add_breakdown" title="Add Breakdown">
                    <i class="fa fa-plus"></i> Add Breakdown
                </a>
                <a href="#" v-if="breakdowns.length" class="btn btn-success btn-sm" @click.prevent="copy"><i
                            class="fa fa-copy"></i> Copy</a>
                <a href="#" v-if="copied_wbs_id" class="btn btn-primary btn-sm" @click.prevent="paste"><i
                            class="fa fa-paste"></i> Paste</a>
            @endcan

            @can('wipe')
                <a href="#WipeBreakdownModal" @{{breakdown.id}} data-toggle="modal" class="btn btn-danger btn-sm"><i
                            class="fa fa-trash"></i>
                    Delete all</a>
            @endcan
        </div>
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
                    {{Form::text('cost_account', session('filters.breakdown.' . $project->id . '.cost_account'), ['class' => 'form-control', 'v-model' => 'cost_account'])}}
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::label('resource_type', 'Resource Type', ['class' => 'control-label'])}}
                    <div class="btn-group btn-group-sm btn-group-block">
                        <a href="#ResourceTypeModal" data-toggle="modal"
                           class="tree-open btn btn-default btn-block">{{session('filters.breakdown.'.$project->id.'.resource_type')? App\ResourceType::with('parent')->find(session('filters.breakdown.'.$project->id.'.resource_type'))->path : 'Select Resource Type' }}</a>
                        <a href="#" @click="resource_type = ''" class="remove-tree-input btn btn-warning" data-target="
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

        <section v-if="filtered_breakdowns.length || count">
            <div class="scrollpane">
                <table class="table table-condensed table-striped table-hover table-breakdown">
                    <thead>
                    <tr>
                        @can('breakdown', $project)
                            <th style="min-width: 32px; max-width: 32px;">&nbsp;</th>
                        @endcan
                        <th style="min-width: 300px; max-width: 300px;" class="bg-blue">Activity ID</th>
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
                        <th style="min-width: 150px; max-width: 150px;" class="bg-black">BOQ Equivalent Unit Rate</th>
                        <th style="min-width: 150px; max-width: 150px;" class="bg-blue">No. Of Labors</th>
                        <th style="min-width: 150px; max-width: 150px;" class="bg-info">Productivity (Unit/Day)</th>
                        <th style="min-width: 150px; max-width: 150px;" class="bg-blue">Productivity Ref</th>
                        <th style="min-width: 150px; max-width: 150px;" class="bg-green">Remarks</th>
                    </tr>
                    </thead>
                </table>
                <table class="table table-condensed table-striped table-hover table-breakdown">
                    <tbody>
                    <tr v-for="breakdown in filtered_breakdowns">
                        @can('breakdown', $project)
                            <td style="min-width: 32px; max-width: 32px;">

                                <form action="/breakdown-resource/@{{ breakdown.breakdown_resource_id }}"
                                      @submit.prevent="destroy(breakdown.breakdown_resource_id)" class="dropdown">
                                    {{csrf_field()}} {{method_field('delete')}}
                                    <button data-toggle="dropdown" type="button"
                                            class="btn btn-default btn-xs dropdown-toggle">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                        <li><a href="/breakdown-resource/@{{ breakdown.breakdown_resource_id }}/edit"
                                               class="btn btn-link in-iframe" title="Edit Resource"><i
                                                        class="fa fa-fw fa-edit"></i> Edit</a></li>
                                        <li><a href="/breakdown/duplicate/@{{ breakdown.breakdown_id }}"
                                               class="btn btn-link in-iframe" title="Duplicate breakdown"><i
                                                        class="fa fa-fw fa-copy"></i> Duplicate</a></li>
                                        <li>
                                            <button class="btn btn-link"><i class="fa fa-fw fa-trash"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </form>
                            </td>
                        @endcan
                        <th style="min-width: 300px; max-width: 300px;" class="bg-blue">@{{ breakdown.code }}</th>
                        <td style="min-width: 300px; max-width: 300px;" class="bg-blue">@{{ breakdown.activity }}</td>
                        <td style="min-width: 150px; max-width: 150px;" class="bg-black">@{{ breakdown.template }}</td>
                        <td style="min-width: 150px; max-width: 150px;"
                            class="bg-blue">@{{ breakdown.cost_account }}</td>
                        <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{breakdown.eng_qty }}</td>
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
                        <td style="min-width: 150px; max-width: 150px;" class="bg-info">@{{ breakdown.unit_price }}</td>
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
                        <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{ breakdown.remarks }}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :total="count"></pagination>
        </section>
        <div class="alert alert-info" v-else><i class="fa fa-info-circle"></i> No breakdowns found</div>


        @can('wipe')
            <div class="modal fade" id="WipeBreakdownModal" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <form method="post" action="/breakdown/wipe/@{{ wbs_id }}" class="modal-content">
                        {{csrf_field()}} {{method_field('delete')}}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            <h4 class="modal-title">Delete all breakdown</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you
                                want to delete all breakdowns in this wbs-level?
                            </div>
                            <input type="hidden" name="delete" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" v-on:click="wipeAll" :disabled="wiping">
                                <i class="fa fa-@{{ wiping? 'spinner fa-spin' : 'trash' }}"></i> Wipe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endcan
    </section>
</template>

<breakdown project="{{$project->id}}"></breakdown>