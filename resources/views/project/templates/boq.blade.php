<template id="BOQTemplate">

    <div class="boq">
        <div class="loader" v-show="loading">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>

        <div class="form-group tab-actions pull-right">
            @can('boq', $project)
                <a href="/boq/create?project={{$project->id}}&&wbs_id=@{{wbs_id}}" class="btn btn-primary btn-sm in-iframe" title="Add BOQ item">
                    <i class="fa fa-plus"></i> Add item
                </a>
            @endcan

            @can('boq-divisions')
                <a href="{{route('boq-division.index')}}" class="btn btn-primary btn-sm" target="_blank">Manage Divisions</a>
            @endcan
            @can('wipe')
                <a href="#WipeBoqModal" data-toggle="modal" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete All</a>
            @endcan
        </div>
        <div class="clearfix"></div>


        <section v-if="!empty_boq" class="panel-group" id="BoqAccord">
            <div class="panel panel panel-primary panel-collapse" v-for="(discipline, items) in boq">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#BoqAccord" href="#@{{ (discipline || 'General')|slug }}">@{{ discipline || 'General' }}</a>
                    </h4>
                </div>

                <table class="table table-condensed table-striped table-hover table-fixed collapse"
                       id="@{{ (discipline || 'General')|slug }}">
                    <thead>
                    <tr>
                        <th class="col-md-6">BOQ Item</th>
                        <th class="col-md-3">Cost Account</th>
                        <th>
                        @can('boq', $project)
                        Actions
                        @endcan
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items">
                        <td class="col-md-6">@{{item['description']}}</td>
                        <td class="col-md-3">@{{item['cost_account']}}</td>
                        <td>
                            @can('boq', $project)
                                <form action="/boq/@{{item.id}}" method="post" @submit.prevent="destroy(item.id)">
                                    {{csrf_field()}} {{method_field('delete')}}
                                    <a href="/boq/@{{ item.id }}/edit" class="btn btn-sm btn-primary in-iframe" title="Edit BOQ">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <div class="alert alert-info" v-else>
            <i class="fa fa-info-circle"></i> No items found
        </div>

        @can('wipe')
        <div class="modal fade" id="WipeBoqModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <form method="post" action="{{route('boq.wipe', $project)}}" class="modal-content">
                    {{csrf_field()}} {{method_field('delete')}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <h4 class="modal-title">Delete all BOQ</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Are you sure you want
                            to delete all BOQ in the project?
                        </div>
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

</template>
<boq project="{{$project->id}}"></boq>