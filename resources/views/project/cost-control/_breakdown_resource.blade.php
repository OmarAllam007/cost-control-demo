<breakdown-resource inline-template v-for="resource in activity.resources"
                    :activity="activity" :resource="resource"
                    :class="resource.is_rollup? 'highlight' : '' "
                    @show_delete_resource="deleteResource(resource)"
                    @show_delete_activity="deleteActivity(resource)">

    <article class="card breakdown-resource">
        <div class="card-body display-flex">
            <section class="information flex">
                <div class="display-flex mb-1">
                    <strong>@{{resource.resource_type}} / @{{resource.resource_code}} &mdash;
                        @{{resource.resource_name}}</strong>
                    <span><span class="tag">Status: </span>@{{resource.status || "Not Started" }} (@{{resource.progress|number_format}}%)</span>
                    <strong><span class="tag">Cost Account: </span>@{{resource.cost_account}}</strong>
                </div>

                <table class="table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th width="20%">&nbsp;</th>
                        <th width="16%"><span class="tag">Qty</span></th>
                        <th width="16%"><span class="tag">Unit Price</span></th>
                        <th width="16%"><span class="tag">Cost</span></th>
                        <th width="16%"><span class="tag">Allowable Cost</span></th>
                        <th width="16%"><span class="tag">Variance</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th><span class="tag">Budget</span></th>
                        <td>@{{resource.budget_unit|number_format}}</td>
                        <td>@{{resource.unit_price|number_format}}</td>
                        <td>@{{resource.budget_cost|number_format}}</td>
                        <td>&mdash;</td>
                        <td>&mdash;</td>
                    </tr>

                    <tr class="extended" v-if="expanded">
                        <th><span class="tag">Previous</span></th>
                        <td>@{{resource.prev_qty|number_format}}</td>
                        <td>@{{resource.prev_unit_price|number_format}}</td>
                        <td>@{{resource.prev_cost|number_format}}</td>
                        <td>&mdash;</td>
                        <td>&mdash;</td>
                    </tr>

                    <tr class="extended" v-if="expanded">
                        <th><span class="tag">Current</span></th>
                        <td>@{{resource.curr_qty|number_format}}</td>
                        <td>@{{resource.curr_unit_price|number_format}}</td>
                        <td>@{{resource.curr_cost|number_format}}</td>
                        <td>&mdash;</td>
                        <td>&mdash;</td>
                    </tr>

                    <tr>
                        <th><span class="tag">To Date</span></th>
                        <td>@{{resource.to_date_qty|number_format}}</td>
                        <td>@{{resource.to_date_unit_price|number_format}}</td>
                        <td>@{{resource.to_date_cost|number_format}}</td>
                        <td>@{{resource.allowable_ev_cost|number_format}}</td>
                        <td :class="resource.allowable_var < 0 ? 'text-danger' : 'text-success'">
                            @{{resource.allowable_var|number_format}}
                        </td>
                    </tr>

                    <tr class="extended" v-if="expanded">
                        <th><span class="tag">Remaining</span></th>
                        <td>@{{resource.remaining_qty|number_format}}</td>
                        <td>@{{resource.remaining_unit_price|number_format}}</td>
                        <td>@{{resource.remaining_cost|number_format}}</td>
                        <td>&mdash;</td>
                        <td>&mdash;</td>
                    </tr>

                    <tr>
                        <th><span class="tag">At Completion</span></th>
                        <td>@{{resource.completion_qty|number_format}}</td>
                        <td>@{{resource.completion_unit_price|number_format}}</td>
                        <td>@{{resource.completion_cost|number_format}}</td>
                        <td>@{{resource.budget_cost|number_format}}</td>
                        <td :class="resource.cost_var < 0 ? 'text-danger' : 'text-success'">
                            @{{resource.cost_var|number_format}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

            <section class="actions">
                <button v-if="resource.important" class="btn btn-xs btn-danger" disabled>
                    <i class="fa fa-asterisk fa-fw"></i>
                </button>

                <div class="dropdown">
                    <a href="#" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="Menu">
                        <i class="fa fa-bars fa-fw"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">
                        @can('manual_edit', $project)
                            <li>
                                <a class="in-iframe"
                                   :href="'/cost/' + resource.breakdown_resource_id + '/pseudo-edit'"
                                   title="Edit Resource Data"><i class="fa fa-fw fa-edit"></i> Edit Resource</a>
                            </li>
                        @endcan

                        @can('delete_resources', $project)
                            <li>
                                <a href="#" @click.prevent="deleteResource(resource)" title="Delete resource data">
                                    <i class="fa fa-fw fa-trash"></i> Delete Resource Data
                                </a>
                            </li>
                            <li>
                                <a href="#" @click.prevent="deleteActivity(resource)" title="Delete activity data">
                                <span class="text-danger">
                                    <i class="fa fa-fw fa-remove"></i> Delete Activity Data
                                </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>

                <button title="More Information" class="btn btn-xs btn-default" @click="expanded = !expanded">
                    <i :class="{'fa fa-fw': true, 'fa-angle-down': !expanded, 'fa-angle-up': expanded}"></i>
                </button>
            </section>
        </div>

    </article>
</breakdown-resource>