<template id="WbsTemplate">
    <div class="panel-body wbs-tree-container">
        <ul class="wbs-tree" id="wbs-tree">
            <wbs-item v-for="item in wbs_levels" :item="item"></wbs-item>
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

<template id="WbsItemTemplate">
    <li>
        <div class="wbs-item">
            <a href="#wbsChildren@{{item.id}}" class="wbs-icon" data-toggle="collapse" v-if="item.children.length"><i
                        class="fa fa-plus-square-o toggle-icon"></i></a>
            <span class="wbs-icon" v-else><i class="fa fa-angle-right wbs-icon"></i></span>

            <a href="#" class="wbs-link" @click.prevent="setSelected()" title="@{{ item.name }}" data-toggle="tooltip" data-placement="right">
                @{{item.code}}
            </a>
        </div>


        <ul class="collapse" id="wbsChildren@{{item.id}}" v-if="item.children && item.children.length">
            <wbs-item v-for="child in item.children" :item="child"></wbs-item>
        </ul>

    </li>
</template>

<wbs :wbs_levels="{{json_encode($wbsTree)}}" project="{{$project->id}}"></wbs>
