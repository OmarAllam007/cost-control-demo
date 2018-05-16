<template>
    <table class="table table-bordered table-condensed table-hover table-striped">
        <thead>
        <tr @click="checkAll">
            <td class="text-center"><input type="checkbox" v-model="check_all"></td>
            <td>Resource Code</td>
            <td>Resource Name</td>
            <td>Remarks</td>
            <td>Budget Unit</td>
            <td>Unit of measure</td>
            <td>Budget Cost</td>
            <td>To Date Qty</td>
            <td>To Date Cost</td>
            <td>Progress</td>
        </tr>
        <tr class="info">
            <th class="text-center">&nbsp;</th>
            <th v-text="cost_account.code"></th>
            <th v-text="cost_account.description"></th>
            <th></th>
            <th>
                <input type="text" class="form-control input-sm" :name="`budget_unit[${cost_account.id}]`" placeholder="Budget Qty" :value="budget_qty" :required="selected">
            </th>
            <th>
                <select class="form-control input-sm" :name="`measure_unit[${cost_account.id}]`" :required="selected">
                    <option value="">Select Unit</option>
                    <option v-for="unit in units" :selected="unit.id == qs_unit" :value="unit.id" v-text="unit.type" :key="unit.id"></option>
                </select>
            </th>
            <th>{{total_budget_cost}}</th>
            <th>
                <input type="text" class="form-control input-sm" :name="`to_date_qty[${cost_account.id}]`" placeholder="To date Qty" :required="selected">
            </th>
            <th>{{total_to_date_cost}}</th>
            <th>
                <input type="text" class="form-control input-sm" :name="`progress[${cost_account.id}]`" placeholder="Progress" :required="selected">
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="resource in cost_account.resources" :class="resource.important? 'danger' : ''" @click="resource.selected = !resource.selected" :key="resource.id">
            <td class="text-center">
                <input type="checkbox" :name="get_input_name(resource)"
                       :value="resource.id"
                       @change="$emit('state-changed')"
                       v-model="resource.selected"
                       v-if="!resource.important">
            </td>
            <td v-text="resource.code"></td>
            <td v-text="resource.name"></td>
            <td v-text="resource.remarks"></td>
            <td>{{resource.budget_unit | number_format}}</td>
            <td>{{resource.measure_unit}}</td>
            <td>{{resource.budget_cost | number_format}}</td>
            <td>{{resource.to_date_qty|number_format}}</td>
            <td>{{resource.to_date_cost|number_format}}</td>
            <td>{{resource.progress|number_format}}%</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        props: ['initial'],

        data() {
            return {
                cost_account: this.initial,
                show_children: false, loading: false,
                check_all: false,
                units: window.units
            }
        },

        computed: {
            total_budget_cost() {
                let total = 0;

                this.cost_account.resources.forEach(function(resource) {
                    total += resource.budget_cost;
                });

                return parseFloat(total.toFixed(2)).toLocaleString();
            },

            total_to_date_cost() {
                let total = 0;
                this.cost_account.resources.forEach(function(resource) {
                    total += resource.to_date_cost;
                });

                return parseFloat(total.toFixed(2)).toLocaleString();
            },

            budget_qty() {
                if (this.cost_account.resources.length) {
                    return this.cost_account.resources[0].budget_qty;
                }

                return 1;
            },

            selected() {
                for (let res in this.cost_account.resources) {
                    if (this.cost_account.resources[res].selected) {
                        return true;
                    }
                }
            },

            qs_unit() {
                if (this.cost_account.resources.length > 0) {
                    return this.cost_account.resources[0].qs_unit;
                }

                return 0;
            }
        },

        methods: {
            toggleChildren() {
                this.show_children = !this.show_children;

                if (!this.resources.length) {
                    this.loading = true;

                    $.ajax({
                        url: `/api/rollup/cost-account/${this.cost_account.wbs_id}/${this.cost_account.id}`,
                        dataType: 'json'
                    }).then(data => {
                        this.loading = false;
                        this.resources = data;
                    }, () => {
                        this.loading = false;
                    });
                }
            },

            hasResources() {
                return Object.keys(this.resources).length;
            },

            checkAll() {
                this.setChecked(!this.check_all);
            },

            get_input_name(resource, type = 'resources') {
                return `${type}[${this.cost_account.id}][${resource.id}]`;
            },

            setChecked(state = true) {
                this.check_all = state;

                this.cost_account.resources.filter(resource => !resource.important).forEach(resource => {
                    resource.selected = this.check_all;
                });
            }
        },

        filters: {
            number_format(val) {
                if (!val) {
                    val = 0;
                }
                return parseFloat(val.toFixed(2)).toLocaleString();
            }
        }
    };
</script>