<template>
    <article class="card cost-account-activity">
        <div v-show="loading" class="loading"><i class="fa fa-3x fa-spinner fa-spin"></i></div>
        <div class="card-title display-flex">
            <h4 class="flex">
                <input type="checkbox" title="Select All" v-model="select_all" value="1"/>

                <a href="#" @click.prevent="expanded = ! expanded">{{activity.name}} &mdash; {{activity.code}}</a>
            </h4>

            <div class="vw-300 display-flex">
                <input type="search" v-model="search" placeholder="Type to search" class="form-control input-sm">
                <button v-show="show_rollup_button" type="button" class="btn btn-sm btn-primary vml-1" @click="rollup">
                    <i class="fa fa-compress"></i> Rollup
                </button>
            </div>
        </div>

        <div class="card-body" v-show="expanded">
            <cost-account v-for="cost_account in filtered_cost_accounts"
                          :key="cost_account.code"
                          :initial="cost_account"
                          @state-changed="update_selected"></cost-account>
        </div>
    </article>
</template>

<style>
    .vml-1 {
        margin-left: 0.5rem;
    }

    .vw-300 {
        width: 300px;
    }

    .card h4 {
        margin: 0;
    }

    .cost-account-activity {
        position: relative;
    }

    .cost-account-activity .loading {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(255, 255, 255, 0.7);
    }
</style>

<script>
    export default {
        name: 'CostAccountActivity',

        props: ['initial'],

        data() {
            return {
                activity: this.initial, cost_accounts: [],
                expanded: false, loading: false, search: '',
                select_all: false, show_rollup_button: false
            };
        },

        created() {
            this.loadCostAccounts();
        },

        computed: {
            filtered_cost_accounts() {
                if (!this.search) {
                    return this.cost_accounts;
                }

                const term = this.search.toLowerCase();
                return this.cost_accounts.filter(cost_account => {
                    return cost_account.code.toLowerCase().indexOf(term) >= 0 ||
                        cost_account.description.toLowerCase().indexOf(term) >= 0;
                });
            }
        },

        watch: {
            select_all(state) {
                if (state) {
                    this.expanded = true;
                }
                this.$children.forEach(child => child.setChecked(state));
            }
        },

        methods: {
            update_selected() {
                const selected = this.$children.filter(child => {
                    return child.selected;
                }).length;
                this.show_rollup_button = selected > 0;
                if (!selected) {
                    this.select_all = false;
                }
            },

            loadCostAccounts() {
                return $.ajax({
                    url: `/api/rollup/activities/${this.activity.wbs_id}/${this.activity.activity_id}`,
                    dataType: 'json'
                }).then(data => {
                    this.cost_accounts = data;
                    if (!this.cost_accounts.length) {
                        this.$emit('delete-activity', this.activity);
                    }
                    this.loading = false;
                }, () => {
                    this.loading = false;
                });
            },

            rollup() {
                const _token = document.querySelector('meta[name=csrf-token]').content;
                const cost_accounts = this.$children.filter(
                    child => child.selected
                );

                const ids = cost_accounts.map(child => child.cost_account.id);

                const budget_unit = cost_accounts.reduce((items, child) => {
                    items[child.cost_account.id] = child.budget_unit;
                    return items
                }, {});

                const measure_unit = cost_accounts.reduce((items, child) => {
                    items[child.cost_account.id] = child.measure_unit;
                    return items
                }, {});

                const to_date_qty = cost_accounts.reduce((items, child) => {
                    items[child.cost_account.id] = child.to_date_qty;
                    return items
                }, {});

                const progress = cost_accounts.reduce((items, child) => {
                    items[child.cost_account.id] = child.progress;
                    return items
                }, {});

                this.loading = true;
                $.ajax({
                    url: `/project/${this.activity.project_id}/rollup-cost-account`,
                    dataType: 'json', method: 'post',
                    data: {_token, cost_account: ids, budget_unit, measure_unit, to_date_qty, progress}
                }).then(response => {
                    this.loadCostAccounts();
                }, response => {
                    this.loading = false;
                });
            }
        }
    };
</script>