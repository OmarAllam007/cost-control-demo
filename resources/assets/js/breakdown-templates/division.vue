<template>
    <li>
        <article v-if="should_show()">
            <div class="wbs-item" :class="{active: division.id == root.division}">
            <span class="wbs-icon">
                <a @click.prevent="collapsed = !collapsed" :href="`#division-${division.id}`">
                    <i class="fa" :class="collapsed? 'fa-plus-square-o':'fa-minus-square-o'"></i>
                </a>
            </span>
                <a :href="`#division-${division.id}`" class="wbs-link" v-text="division.label"
                   @click.prevent="change_division(division.id)"></a>
            </div>

            <ul :id="`division-${division.id}`" :class="{collapse: collapsed}">
                <division v-for="division in division.subtree" :division="division" :search="search"></division>

                <li v-for="activity in filtered_activities">
                    <div class="wbs-item" :class="{active: activity.id == root.activity}">
                        <span class="wbs-icon"><i class="fa fa-caret-right"></i></span>
                        <a :href="`#activity-${activity.id}`" v-text="activity.name"
                           @click.prevent="change_activity(activity.id)"></a>
                    </div>
                </li>
            </ul>
        </article>
    </li>
</template>

<script>
    export default {
        name: "division",

        props: ['division', 'search'],

        data() {
            let root = this;
            while (root && root.constructor.name !== 'BreakdownTemplate') {
                root = root.$parent;
            }
            return {collapsed: true, root};
        },

        computed: {
            filtered_activities() {
                const term = this.filter.toLowerCase();
                return this.division.activities.filter(activity => {
                    return activity.name.toLowerCase().indexOf(term) >= 0;
                });
            },

            filter() {
                return this.search || '';
            }

        },

        methods: {
            change_division(division_id) {
                this.root.division = division_id;
                this.root.activity = 0;
            },

            change_activity(activity_id) {
                this.root.activity = activity_id;
                this.root.division = 0;
            },

            should_show() {
                const term = this.filter.toLowerCase();
                if (this.division.label.toLowerCase().indexOf(term) >= 0) {
                    return true;
                }

                for (let index in this.$children) {
                    const child = this.$children[index];
                    if (child.should_show()) {
                        return true;
                    }
                }

                return !!this.filtered_activities.length;
            }
        }
    }
</script>