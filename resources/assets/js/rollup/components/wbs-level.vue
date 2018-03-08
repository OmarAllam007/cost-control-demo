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
        </ul>

        <ul :class="{'collapse' : true, 'in': show_children}">
            <div style="padding-top: 15px; padding-bottom: 20px" v-if="hasActivityRollup && activities.length">
                <a href="#" @click.prevent="checkAll(true)"><i class="fa fa-check-square-o"></i> Select All</a> &verbar;
                <a href="#" @click.prevent="checkAll(false)"><i class="fa fa-times"></i> Remove All</a>
            </div>

            <activity v-for="activity in activities" :initial="activity" :depth="depth + 1"></activity>
            <li v-if="loading"><i class="fa fa-refresh fa-spin"></i></li>
        </ul>
    </li>
</template>

<script>
    export default {
        name: 'wbs-level',

        props: ['initial', 'depth', 'hasActivityRollup'],

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
            },

            checkAll(state = true) {
                if (this.hasActivityRollup) {
                    this.$children
                        .filter(child => child.constructor.name === 'Activity')
                        .forEach(child => child.setChecked(state));
                }
            }
        }
    }

</script>