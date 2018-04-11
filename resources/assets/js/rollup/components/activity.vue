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

            <li v-if="cost_accounts.length" class="check-all-box">
                <div class="display-flex">
                    <div>
                        <a href="#" @click.prevent="checkAll(true)"><i class="fa fa-check-square-o"></i> Select All</a> &verbar;
                        <a href="#" @click.prevent="checkAll(false)"><i class="fa fa-times"></i> Remove All</a>
                    </div>

                    <div style="width: 200px;">
                        <input type="search" v-model="search" class="form-control" placeholder="Cost Account Search">
                    </div>
                </div>

            </li>

            <cost-account v-for="cost_account in filtered_cost_accounts" :initial="cost_account"></cost-account>

            <li v-if="loading"><i class="fa fa-refresh fa-spin"></i></li>
        </ul>
    </li>
</template>

<style>
    .check-all-box {
        padding-top: 10px;
        padding-bottom: 10px;
    }
</style>

<script>
    export default {
        props: ['initial', 'depth'],

        data() {
            return { activity: this.initial, cost_accounts: [], show_children: false, loading: false, search: '' }
        },

        computed: {
            filtered_cost_accounts() {
                if (!this.search) {
                    return this.cost_accounts;
                }

                const term = this.search.toLowerCase();
                return this.cost_accounts.filter(
                    cost_account => cost_account.code.toLowerCase().indexOf(term) >= 0
                )
            }
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
            },

            checkAll(state) {
                this.$children.forEach(child => child.setChecked(state));
            }
        }
    };
</script>