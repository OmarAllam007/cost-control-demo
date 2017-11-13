<qty-survey project="{{$project->id}}" inline-template>
    <div class="qty-survey">
        @can('qty_survey', $project)
        <div class="form-group tab-actions clearfix">
            <div class="pull-right">

                <a href="/survey/create?project={{$project->id}}&&wbs_id=@{{wbs_id}}" class="btn btn-primary btn-sm in-iframe">
                    <i class="fa fa-plus"></i> Add Quantity Survey
                </a>
            </div>
        </div>
        @endcan

        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <section class="filters row" id="qty-survey-filters">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="Search" class="control-label">Search</label>
                    <input type="text" class="form-control" id="Search" v-model="filter" placeholder="Type here to search in cost account or description">
                </div>
            </div>
        </section>

        <section id="QtyList" v-if="filtered_qty.length || count">
            <table class="table table-condensed table-striped table-hover table-fixed">
                <thead>
                <tr>
                    <th class="col-xs-2">Item Code</th>
                    <th class="col-xs-2">Cost Account</th>
                    <th class="col-xs-3">Description</th>
                    <th class="col-xs-1">Budget Quantity</th>
                    <th class="col-xs-1">Eng Quantity</th>
                    <th class="col-xs-1">U.O.M</th>
                    <th class="col-xs-2">
                        @can('qty_survey', $project) Action @endcan
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="quantity in filtered_qty">
                    <td class="col-xs-2">@{{ quantity.item_code}}</td>
                    <td class="col-xs-2">@{{ quantity.cost_account}}</td>
                    <td class="col-xs-3">@{{ quantity.description}}</td>
                    <td class="col-xs-1">@{{ quantity.budget_qty}}</td>
                    <td class="col-xs-1">@{{ quantity.eng_qty}}</td>
                    <td class="col-xs-1">@{{ quantity.unit.type}}</td>
                    <td class="col-xs-2">
                        @can('qty_survey', $project)
                            <form action="/survey/@{{quantity.id}}" method="post" @submit.prevent="destroy(quantity.id)" class="delete_form" data-name="QS">
                                {{csrf_field()}}{{method_field('delete')}}
                                <a href="/survey/@{{quantity.id}}/edit" class="btn btn-sm btn-primary in-iframe" title="Edit Quantity Survey"><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        @endcan
                    </td>
                </tr>
                </tbody>
            </table>

            <pagination :total="count"></pagination>
        </section>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No quantities found
        </div>
    </div>
</qty-survey>