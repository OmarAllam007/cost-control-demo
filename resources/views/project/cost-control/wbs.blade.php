<template id="WbsTemplate">
    <div class="panel-body wbs-tree-container">
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Type here to filter" v-model="filter">
        </div>
        <ul class="wbs-tree" id="wbs-tree">
            <wbs-item v-for="item in filtered_wbs_levels" :item="item" :has-filter="!!filter"></wbs-item>
        </ul>
    </div>
</template>

@include('project.cost-control.wbs-item')

<wbs :wbs_levels="{{json_encode($wbsTree)}}" project="{{$project->id}}"></wbs>
