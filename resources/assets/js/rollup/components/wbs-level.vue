<template>
    <li :class="`level-${depth}`">
        <div class="wbs-item">
            <strong>
                <a href="#children-{{level.id}}" class="open-level" @click.prevent="toggleChildren">
                    <span class="wbs-icon"><i class="fa" :class="show_children? 'fa-minus-square-o' : 'fa-plus-square-o'"></i></span>
                    {{level.name}}
                    <small>({{level.code}})</small>
                </a>
            </strong>
        </div>

        <ul v-if="level.children.length" :class="{'collapse' : this.depth, 'in': show_children}">
            <wbs-level :initial="sublevel" v-for="sublevel in level.children" :depth="depth + 1" :name="sublevel.id"></wbs-level>
            <li v-if="loading"><i class="fa fa-refresh fa-spin"></i></li>
        </ul>

        <ul  v-if="activities.length" :class="{'collapse' : true, 'in': show_children}">
            <activity v-for="activity in activities" :initial="activity" :depth="depth + 1"></activity>
        </ul>
    </li>
</template>

<script>
    import Activity from './activity.vue';

    export default {
        name: 'wbs-level',

        props: ['initial', 'depth'],

        data() {
            return {
                level: this.initial,
                activities: [],
                show_children: false,
                loading: false
            };
        },

        methods: {
            toggleChildren() {
                this.show_children = !this.show_children;

                if (!this.activities.length) {
                    this.loading = true;

                    $.ajax({
                        url: `/api/rollup/wbs/${this.level.id}`,
                        dataType: 'json'
                    }).then((data) => {
                        this.activities = data;
                        this.loading = false;
                    }, () => {
                        this.loading = false;
                    });
                }
            }
        },

        components: {
            Activity
        }
    }

</script>