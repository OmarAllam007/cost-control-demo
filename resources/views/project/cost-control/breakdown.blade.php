<template id="BreakdownTemplate">

    <div class="breakdown">

        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

            <a href="{{route('costshadow.export',$project)}}" class="btn btn-info btn-sm pull-right">
                <i class="fa fa-cloud-download"></i> Export
            </a>
<div class="clearfix"></div>
        <section class="filters row" id="breakdown-filters">
            @include('std-activity._modal', ['input' => 'activity', 'value' => ''])
            @include('resource-type._modal', ['input' => 'resource_type', 'value' => ''])

            <div class="col-sm-3">
                <div class="form-group form-group-sm">
                    {{Form::label('activity', 'Activity', ['class' => 'control-label'])}}
                    <div class="btn-group btn-group-sm btn-group-block">
                        <a href="#ActivitiesModal" data-toggle="modal" class="btn btn-default btn-block tree-open">{{ session('filters.breakdown.'.$project->id.'.activity')? App\StdActivity::find(session('filters.breakdown.'.$project->id.'.activity'))->name : 'Select Activity' }}</a>
                        <a href="#" @click="activity = ''" class="remove-tree-input btn btn-warning" data-target="#ActivitiesModal" data-label="Select Activity"><span class="fa fa-times-circle"></span></a>
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
                        <a href="#ResourceTypeModal" data-toggle="modal" class="tree-open btn btn-default btn-block">{{session('filters.breakdown.'.$project->id.'.resource_type')? App\ResourceType::with('parent')->find(session('filters.breakdown.'.$project->id.'.resource_type'))->path : 'Select Resource Type' }}</a>
                        <a href="#" @click="resource_type = ''" class="remove-tree-input btn btn-warning" data-target="#ResourceTypeModal" data-label="Select Resource Type"><span class="fa fa-times-circle"></span></a>
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

        <div class="scrollpane" v-if="filtered_breakdowns.length">
            <table class="table table-condensed table-striped table-hover table-breakdown">
                <thead>
                <tr>
                    @can('manual_edit')
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
                    <th style="min-width: 150px; max-width: 150px;" class="bg-black">BOQ Equivalent Unit Rate</th>
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
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">To Date Price / Unit ( Eqv. )</th>
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
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance To Date Due to Unit Price</th>
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Allowable Quantity</th>
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance Remaining Due to Unit Price</th>
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance Completion Due to Unit Price</th>
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance Completion Due to Qty</th>
                    <th class="bg-orange" style="min-width: 150px; max-width: 150px;">Cost Variance to Date Due to Qty</th>
                </tr>
                </thead>
            </table>
            <table class="table table-condensed table-striped table-hover table-breakdown">
                <tbody>
                <tr v-for="breakdown in filtered_breakdowns">
                    @can('manual_edit', $project)
                    <td style="min-width: 30px; max-width: 30px">
                        <div class="dropdown">
                            <button class="btn btn-xs btn-default" type="button" data-toggle="dropdown"><span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="#" @click.prevent="editResource(breakdown)"><i class="fa fa-fw fa-edit"></i> Edit</a></li>
                                <li><a href="#" @click.prevent="deleteResource(breakdown)"><i class="fa fa-fw fa-trash"></i> Delete resource data</a></li>
                                <li><a href="#" @click.prevent="deleteActivity(breakdown)"><i class="fa fa-fw fa-remove"></i> Delete activity data</a></li>
                            </ul>
                        </div>
                    </td>
                    @endcan
                    <td style="min-width: 300px; max-width: 300px;" class="bg-blue">@{{ breakdown.activity }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-black">@{{ breakdown.template }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-blue">@{{ breakdown_account }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{breakdown.eng_qty }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{ breakdown.budget_qty }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-blue">@{{ breakdown.resource_qty }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-info">@{{ breakdown.resource_waste }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-green">@{{ breakdown.resource_type }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-green">@{{ breakdown.resource_code }}</td>
                    <td style="min-width: 200px; max-width: 200px;"
                        class="bg-green">@{{ breakdown.resource_name }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-info">@{{ breakdown.unit_price }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-info">@{{ breakdown.measure_unit }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{ breakdown.budget_unit }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{ breakdown.budget_cost }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-black">@{{ breakdown.boq_equivilant_rate }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-blue">@{{ breakdown.labors_count }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-info">@{{ breakdown.productivity_output }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-blue">@{{ breakdown.productivity_ref }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-green">@{{ breakdown.remarks }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.progress : 0 | number_format }}%</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.status : 'Not Started'}}</td>
                    <td class="bg-green" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.previous_unit_price: 0 |number_format }}</td>
                    <td class="bg-green" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.previous_qty: 0 |number_format }}</td>
                    <td class="bg-green" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.previous_cost: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.current_unit_price: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.current_qty: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.current_cost: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.to_date_unit_price: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.to_date_qty: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.to_date_cost: 0 |number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.allowable_ev_cost : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.allowable_var : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.remaining_unit_price : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.remaining_qty : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.remaining_cost : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.bl_allowable_cost : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.bl_allowable_var : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.completion_unit_price : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.completion_qty : 0 | number_format }}</td>
                    <td class="bg-violet" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.completion_cost : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.qty_var : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.cost_var : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.unit_price_var : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.physical_unit : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px;">@{{ breakdown? breakdown.pw_index : 0 | number_format }}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_to_date_due_unit_price : 0 | number_format}}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.allowable_qty : 0 | number_format}}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_remaining_due_unit_price : 0 | number_format}}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_completion_due_unit_price : 0 | number_format}}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_completion_due_qty : 0 | number_format}}</td>
                    <td class="bg-orange" style="min-width: 150px; max-width: 150px">@{{breakdown? breakdown.cost_variance_to_date_due_qty : 0 | number_format}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-info" v-else><i class="fa fa-info-circle"></i> No breakdowns found</div>
    </div>
</template>

<breakdown project="{{$project->id}}"></breakdown>