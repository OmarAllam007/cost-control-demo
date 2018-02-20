<template>
    <table class="table table-bordered table-condensed table-hover table-striped">
        <thead>
        <tr @click="checkAll">
            <td><input type="checkbox" v-model="check_all"></td>
            <td>Resource Code</td>
            <td>Resource Name</td>
            <td>Remarks</td>
            <td>Budget Unit</td>
            <td>Unit of measure</td>
            <td>Budget Cost</td>
            <td>To Date Qty</td>
            <td>To Date Cost</td>
        </tr>
        <tr class="info">
            <th>&nbsp;</th>
            <th v-text="cost_account.code"></th>
            <th v-text="cost_account.description"></th>
            <th></th>
            <th>
                <input type="number" class="form-control input-sm" :name="`budget_unit[${cost_account.id}]`" placeholder="Budget Unit" value="1">
            </th>
            <th>
                <select class="form-control input-sm" :name="`measure_unit[${cost_account.id}]`">
                    <option value="">Select Unit</option>
                    <option v-for="unit in units" :value="unit.id" v-text="unit.type"></option>
                </select>
            </th>
            <th>{{total_budget_cost|number_format}}</th>
            <th><input type="number" class="form-control input-sm" :name="`to_date_qty[${cost_account.id}]`" placeholder="Budget Unit" value="1"></th>
            <th>{{total_to_date_cost|number_format}}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="resource in cost_account.resources" :class="resource.important? 'danger' : ''" @click="resource.selected = !resource.selected">
            <td>
                <input type="checkbox" :name="get_input_name(resource)"
                       :value="resource.id"
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
                return 0;
            },

            total_to_date_cost() {
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
                this.check_all = !this.check_all;

                this.cost_account.resources.filter(resource => !resource.important).forEach(resource => {
                    resource.selected = this.check_all;
                });
            },

            get_input_name(resource, type = 'resources') {
                return `${type}[${this.cost_account.id}][${resource.id}]`;
            }
        },

        filters: {
            number_format(val) {
                return parseFloat(val.toFixed(2)).toLocaleString();
            }
        }
    };
</script>