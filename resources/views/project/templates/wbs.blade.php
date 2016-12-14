<template id="WbsTemplate">
    <div class="panel-body wbs-tree-container">
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Type here to filter" v-model="filter">
        </div>
        <ul class="wbs-tree" id="wbs-tree">
            <wbs-item v-for="item in filtered_wbs_levels" :item="item" :has-filter="!!filter"></wbs-item>
        </ul>

        <div class="modal fade" tabindex="-1" role="dialog" id="WipeWBSModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        <h4 class="modal-title">Wipe WBS</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            Are you sure you want to delete all WBS for this project?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" v-on:click="wipeAll" :disabled="wiping">
                            <i class="fa fa-@{{ wiping? 'spinner fa-spin' : 'trash' }}"></i> Wipe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@include('project.templates.wbs-item')

<wbs :wbs_levels="{{json_encode($wbsTree)}}" project="{{$project->id}}"></wbs>
