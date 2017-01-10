<qty-survey project="{{$project->id}}" inline-template>
    <div class="qty-survey">
        <div class="form-group tab-actions clearfix">
            <div class="pull-right">
                @can('qty_survey')
                <a href="/survey/create?project={{$project->id}}&&wbs_id=@{{wbs_id}}" class="btn btn-primary btn-sm in-iframe">
                    <i class="fa fa-plus"></i> Add Quantity Survey
                </a>

                <a href="{{route('survey.import', ['project' => $project->id])}}" class="btn btn-success btn-sm in-iframe">
                    <i class="fa fa-cloud-upload"></i> Import
                </a>
                @endcan

                <a href="{{route('survey.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
                    <i class="fa fa-cloud-download"></i> Export
                </a>

                @can('wipe')
                    <a href="#WipeQSModal" data-toggle="modal" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete all</a>
                @endcan
            </div>
        </div>

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
                    <th class="col-xs-2">Cost Account</th>
                    <th class="col-xs-3">Description</th>
                    <th class="col-xs-2">Budget Quantity</th>
                    <th class="col-xs-2">Eng Quantity</th>
                    <th class="col-xs-3">
                        @can('qty_survey', $project) Action @endcan
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="quantity in filtered_qty">
                    <td class="col-xs-2">@{{ quantity.cost_account}}</td>
                    <td class="col-xs-3">@{{ quantity.description}}</td>
                    <td class="col-xs-2">@{{ quantity.budget_qty}}</td>
                    <td class="col-xs-2">@{{ quantity.eng_qty}}</td>
                    <td class="col-xs-3">
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

        @can('wipe')
            <div class="modal fade" id="WipeQSModal" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <form method="post" action="{{route('survey.wipe', $project)}}" class="modal-content">
                        {{csrf_field()}} {{method_field('delete')}}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            <h4 class="modal-title">Delete all quantities</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete all quantities in the project?</div>
                            <input type="hidden" name="wipe" value="1">
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
    </div>
</qty-survey>