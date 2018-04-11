<template>
    <li>
        <div class="wbs-item">
            <span class="wbs-icon">
                <a @click.prevent="collapsed = !collapsed" :href="`#division-${division.id}`">
                    <i class="fa" :class="collapsed? 'fa-plus-square-o':'fa-minus-square-o'"></i>
                </a>
            </span>
            <a :href="`#division-${division.id}`" class="wbs-link" v-text="division.label" @click.prevent="change_division(division.id)"></a>
        </div>

        <ul :id="`division-${division.id}`" :class="{collapse: collapsed}">
            <division v-for="division in division.subtree" :division="division"></division>

            <li v-for="activity in division.activities">
                <div class="wbs-item">
                    <span class="wbs-icon"><i class="fa fa-caret-right"></i></span>
                    <a :href="`#activity-${activity.id}`" v-text="activity.name" @click.prevent="change_activity(activity.id)"></a>
                </div>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        name: "division",

        props: ['division'],

        data() {
            return {collapsed: true};
        },

        methods: {
            change_division(division_id) {
                this.$root.division = division_id;
                this.$root.activity = 0;
            },

            change_activity(activity_id) {
                this.$root.activity = activity_id;
                this.$root.division = 0;
            }
        }
    }
</script>