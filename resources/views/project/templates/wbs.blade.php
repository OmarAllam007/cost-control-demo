<template id="WbsTemplate">
    <ul class="wbs-tree" id="wbs-tree">
        <wbs-item v-for="item in wbs_levels" :item="item"></wbs-item>
    </ul>
</template>

<template id="WbsItemTemplate">
    <li>
        <div class="wbs-item">
            <a href="#wbsChildren@{{item.id}}" class="wbs-icon" data-toggle="collapse" v-if="item.children.length"><i class="fa fa-plus-square-o toggle-icon"></i></a>
            <span class="wbs-icon" v-else><i class="fa fa-angle-right wbs-icon"></i></span>

            <a href="#" class="wbs-link" @click.prevent="setSelected()">
                @{{item.name}} &mdash;
                <small>@{{item.code}}</small>
            </a>
        </div>


        <ul class="collapse" id="wbsChildren@{{item.id}}" v-if="item.children && item.children.length">
            <wbs-item v-for="child in item.children" :item="child"></wbs-item>
        </ul>

    </li>
</template>

<wbs :wbs_levels="{{json_encode($wbsTree)}}" project="{{$project->id}}"></wbs>