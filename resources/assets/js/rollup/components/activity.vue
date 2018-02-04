<template>
    <li :class="`level-${depth}`">
        <div class="wbs-item">
            <a href="#children-{{activity.activity_id}}" class="open-level" @click.prevent="toggleChildren">
                <span class="wbs-icon"><i class="fa" :class="show_children? 'fa-minus-square-o' : 'fa-plus-square-o'"></i></span>
                {{ activity.activity }}
                <small>({{activity.code}})</small>
            </a>
        </div>

        <ul :class="{'collapse' : true, 'in': show_children}">
            <cost-account v-for="cost_account in cost_accounts" :initial="cost_account"></cost-account>

            <li v-if="loading"><i class="fa fa-refresh fa-spin"></i></li>
        </ul>
    </li>
</template>

<script>
    export default {
        props: ['initial', 'depth'],

        data() {
            return { activity: this.initial, cost_accounts: [], show_children: false, loading: false }
        },

        methods: {
            toggleChildren() {
                this.show_children = ! this.show_children;
                this.loading = true;

                if (!this.cost_accounts.length) {
                    $.ajax({
                        url: `/api/rollup/activities/${this.$parent.level.id}/${this.activity.activity_id}`,
                        dataType: 'json'
                    }).then(data => {
                        this.cost_accounts = data;
                        this.loading = false;
                    }, () => {
                        this.loading = false;
                    });
                }
            }
        }
    };
</script>