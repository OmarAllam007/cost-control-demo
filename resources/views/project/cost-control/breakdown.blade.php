<template id="BreakdownTemplate">
    <div class="breakdown">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

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
                    <th style="min-width: 150px; max-width: 150px;">Prev. Price/Unit</th>
                    <th style="min-width: 150px; max-width: 150px;">Prev. Qty</th>
                    <th style="min-width: 150px; max-width: 150px;">Prev. Cost</th>
                    <th style="min-width: 150px; max-width: 150px;">Curr. Price/Unit</th>
                    <th style="min-width: 150px; max-width: 150px;">Curr. Qty</th>
                    <th style="min-width: 150px; max-width: 150px;">Curr. Cost</th>
                    <th style="min-width: 150px; max-width: 150px;">To Date Price / Unit ( Eqv. )</th>
                    <th style="min-width: 150px; max-width: 150px;">To Date. Qty</th>
                    <th style="min-width: 150px; max-width: 150px;">To Date. Cost</th>
                </tr>
                </thead>
            </table>
            <table class="table table-condensed table-striped table-hover table-breakdown">
                <tbody>
                <tr v-for="breakdown in filtered_breakdowns">
                    <td style="min-width: 300px; max-width: 300px;" class="bg-blue">@{{ breakdown.activity }}</td>
                    <td style="min-width: 150px; max-width: 150px;" class="bg-black">@{{ breakdown.template }}</td>
                    <td style="min-width: 150px; max-width: 150px;"
                        class="bg-blue">@{{ breakdown.cost_account }}</td>
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
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.previous_unit_price|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.previous_qty|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.previous_cost|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.current_unit_price|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.current_qty|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.current_cost|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.to_date_unit_price|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.to_date_qty|number_format }}</td>
                    <td style="min-width: 150px; max-width: 150px;">@{{ breakdown.to_date_cost|number_format }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-info" v-else><i class="fa fa-info-circle"></i> No breakdowns found</div>
    </div>
</template>

<breakdown project="{{$project->id}}"></breakdown>