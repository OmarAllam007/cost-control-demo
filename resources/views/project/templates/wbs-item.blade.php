<template id="WbsItemTemplate">
    <li>
        <div class="wbs-item">
            <a href="#wbsChildren@{{item.id}}" class="wbs-icon" data-toggle="collapse" v-if="item.children.length && !hasFilter"><i
                        class="fa fa-plus-square-o toggle-icon"></i></a>
            <span class="wbs-icon" v-else><i class="fa fa-angle-right wbs-icon"></i></span>

            <a href="#" class="wbs-link" @click.prevent="setSelected()" title="@{{ item.name }}" data-toggle="tooltip" data-placement="right">
                @{{item.code}}
            </a>
        </div>


        <ul class="collapse" id="wbsChildren@{{item.id}}" v-if="item.children && item.children.length && !hasFilter">
            <wbs-item v-for="child in item.children" :item="child"></wbs-item>
        </ul>

    </li>
</template>