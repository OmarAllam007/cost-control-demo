<template id="BOQTemplate">
    <div class="breakdown">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <div class="form-group tab-actions pull-right">
            <a href="{{route('boq.create', ['project' => $project->id])}}" class="btn btn-primary btn-sm in-iframe">
                <i class="fa fa-plus"></i> Add item
            </a>

            <a href="{{route('boq-division.index')}}" class="btn btn-primary btn-sm" target="_blank">Manage Divisions</a>

            <a href="{{route('boq.import', $project->id)}}" class="btn btn-success btn-sm in-iframe">
                <i class="fa fa-cloud-upload"></i> Import
            </a>

            <a href="{{route('boq.export', ['project' => $project->id])}}" class="btn btn-info btn-sm">
                <i class="fa fa-cloud-download"></i> Export
            </a>

            @can('wipe')
                <a href="WipeBoqModal" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete All</a>
            @endcan
        </div>
        <div class="clearfix"></div>

        <section class="filters" id="breakdown-filters">

        </section>

        <section v-if="!empty_boq" class="panel-group" id="BoqAccord">
            <div class="panel panel panel-primary panel-collapse" v-for="(discipline, items) in boq">
                <div class="panel-heading">
                    <h4 class="panel-title"><a data-toggle="collapse" data-parent="#BoqAccord" href="#@{{ (discipline || 'General')|slug }}">@{{ discipline || 'General' }}</a>
                    </h4>
                </div>

                <table class="table table-condensed table-striped table-hover table-fixed collapse"
                       id="@{{ (discipline || 'General')|slug }}">
                    <thead>
                    <tr>
                        <th class="col-md-6">BOQ Item</th>
                        <th class="col-md-3">Cost Account</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items">
                        <td class="col-md-6">@{{item['description']}}</td>
                        <td class="col-md-3">@{{item['cost_account']}}</td>
                        <td>
                            <form action="/boq/@{{item.id}}" method="post" @submit.prevent="destroy(item.id)">
                                {{csrf_field()}} {{method_field('delete')}}
                                <a href="/boq/@{{ item.id }}/edit" class="btn btn-sm btn-primary in-iframe" title="Edit BOQ">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No items found
        </div>
    </div>
</template>
<boq></boq>