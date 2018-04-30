import Vue from 'vue';
import _ from 'lodash';

Vue.component('ResourceLog', {
    props: ['resource'],

    computed: {
        first() {
            return this.resource.budget_resources[0];
        },

        budget_unit() {
            let total = 0;
            this.resource.budget_resources.forEach(res => { total += res.budget_unit });
            return total;
        },

        budget_cost() {
            let total = 0;
            this.resource.budget_resources.forEach(res => { total += res.budget_cost });
            return total;
        },

        actual_unit_price() {
            if (!this.actual_qty) {
                return 0;
            }

            return this.actual_cost / this.actual_qty;
        },

        actual_qty() {
            return _.reduce(this.actual_resources, (total, r) => total += r.qty, 0);
        },

        actual_cost() {
            return _.reduce(this.actual_resources, (total, r) => total += r.cost, 0);
        },

        actual_resources() {
            return _.flatMap(this.resource.budget_resources, r => r.actual_resources);
        },

        qty_var() {
            return this.budget_unit - this.actual_qty;
        },

        cost_var() {
            return this.budget_cost - this.actual_cost;
        },

        important() {
            return this.resource.budget_resources.filter(res => res.important).length;
        }
    }
});

Vue.filter('number_format', function(val) {
    return parseFloat(val).toLocaleString();
});

const app = new Vue({
    el: 'body',

    data: {
        resource_search: '', resource_mode: 'all', logs: [], loading: false,
        wbs_id: window.wbs_id, code: window.code
    },

    computed: {
        filteredLogs() {
            const term = this.resource_search.toLowerCase();

            return this.logs.filter(log => {
                return term === '' ||
                    log.code.toLowerCase().indexOf(term) > -1 ||
                    log.name.toLowerCase().indexOf(term) > -1;
            }).filter(log => {
                return this.resource_mode === 'all' ||
                    log.budget_resources.filter(res => res.important).length;
            });
        }
    },

    created() {
        this.loadLogs()
    },

    methods: {
        loadLogs() {
            this.loading = true;

            $.ajax({
                dataType: 'json',
                url: `/api/activity-log/${this.wbs_id}/${this.code}`,
            }).then(data => {
                this.logs = data;
                this.loading = false;
            }, () => {
                this.loading = false;
            });
        }
    }
});