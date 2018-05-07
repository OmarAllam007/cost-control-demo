<template>
    <article class="card cost-account-activity">
        <div class="card-title display-flex">
            <h4 class="flex">
                <a href="#" @click.prevent="expanded = ! expanded">{{activity.name}} &mdash; {{activity.code}}</a>
            </h4>

            <input type="search" v-model="search" placeholder="Type to search" class="form-control input-sm">
        </div>

        <div class="card-body" v-show="expanded">
            <cost-account v-for="cost_account in filtered_cost_accounts" :key="cost_account.code" :initial="cost_account"></cost-account>
        </div>
    </article>
</template>

<style>
    .card-title > input[type=search] {
        width: 200px;
    }

    .card h4 {
        margin: 0;
    }
</style>

<script>
    export default {
        props: ['initial'],

        data() {
            return { activity: this.initial, cost_accounts: [], expanded: false, loading: false, search: '' }
        },

        created() {
            $.ajax({
                url: `/api/rollup/activities/${this.activity.wbs_id}/${this.activity.activity_id}`,
                dataType: 'json'
            }).then(data => {
                this.cost_accounts = data;
                this.loading = false;
            }, () => {
                this.loading = false;
            });
        },

        computed: {
            filtered_cost_accounts() {
                if (!this.search) {
                    return this.cost_accounts;
                }

                const term = this.search.toLowerCase();
                return this.cost_accounts.filter(cost_account => {
                    return cost_account.code.toLowerCase().indexOf(term) >= 0 || cost_account.description.toLowerCase().indexOf(term) >= 0;
                });
            }
        },

        methods: {
            checkAll(state) {
                this.$children.forEach(child => child.setChecked(state));
            }
        }
    };
</script>